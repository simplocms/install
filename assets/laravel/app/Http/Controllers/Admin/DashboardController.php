<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\GAHelper;
use App\Services\Settings\Settings;
use Illuminate\Http\Request;

class DashboardController extends AdminController
{
    /**
     * @var \App\Services\Settings\Settings
     */
    private $settings;

    /**
     * DashboardController constructor.
     * @param \App\Services\Settings\Settings $settings
     */
    public function __construct(Settings $settings)
    {
        parent::__construct();
        $this->settings = $settings;
    }


    /**
     * Dashboard
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->setTitleDescription(
            trans('admin/dashboard.titles.index'), trans('admin/dashboard.descriptions.index')
        );

        $authorizeLink = null;

        $tokenData = $this->settings->get('ga_token');
        $gaProfileId = null;
        $canManage = $this->canBeManagedByCurrentUser();

        if (!$tokenData) {
            $authorizeLink = route('admin.dashboard.authorization');
            $canManage = true;
        } else {
            $gaProfileId = $this->settings->get('ga_profile_id');

            if (!$gaProfileId) {
                $authorizeLink = route('admin.dashboard.profiles');
            }
        }

        return view('admin.dashboard.index', compact('authorizeLink', 'canManage'));
    }


    /**
     * Get chart data.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData(Request $request)
    {
        $profileId = $this->settings->get('ga_profile_id');
        $tokenData = $this->settings->get('ga_token');
        $token = (array)json_decode($tokenData);

        $from = '30daysAgo';
        $to = 'today';
        $metrics = 'ga:sessions,ga:users,ga:pageviews,ga:BounceRate,ga:organicSearches,ga:pageviewsPerSession,ga:newUsers';

        $ga = new GAHelper($token, $profileId);

        /** @var \Google_Service_Analytics_GaData $data */
        $data = $ga->getData($from, $to, $metrics, [
            'dimensions' => 'ga:date'
        ]);
        $totals = $data->getTotalsForAllResults();

        return response()->json([
            'errors' => $ga->getErrors(),
            'headers' => array_keys($totals),
            'rows' => $data->getRows(),
            'totals' => $totals
        ]);
    }


    /**
     * Show authorization form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showAuthorizationForm()
    {
        $this->setTitleDescription(
            trans('admin/dashboard.titles.authorization'),
            trans('admin/dashboard.descriptions.authorization')
        );

        $googleClient = \Google::getClient();
        $authUrl = $googleClient->createAuthUrl();

        return view('admin.dashboard.authorization_form', compact('authUrl'));
    }


    /**
     * Get GA token with code from authorization form.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveGAToken(Request $request)
    {
        if ($this->settings->get('ga_token') && !$this->canBeManagedByCurrentUser()) {
            flash()->warning(trans('admin/dashboard.notifications.no_ga_permission'));
            return redirect()->route('admin.dashboard');
        }

        $validator = \Validator::make($request->all(), [
            'code' => 'required'
        ]);

        $client = \Google::getClient();
        $code = $request->input('code');
        $token = null;

        if (!$validator->fails()) {
            $client->fetchAccessTokenWithAuthCode($code);
            $token = $client->getAccessToken();
            if (!$token) {
                $validator->errors()->add('code', trans('admin/dashboard.notifications.invalid_ga_token'));
            }
        }

        if (!$validator->errors()->isEmpty()) {
            return redirect()->route('admin.dashboard.authorization')
                ->withErrors($validator);
        }

        $this->settings->put('ga_token', json_encode($token));
        $this->settings->put('ga_user_id', auth()->id());
        $this->settings->forget('ga_profile_id');

        return redirect()->route('admin.dashboard.profiles');
    }


    /**
     * Show list of google analytics profiles
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showProfilesList()
    {
        if (!$this->canBeManagedByCurrentUser()) {
            flash()->warning(trans('admin/dashboard.notifications.no_ga_permission'));
            return redirect()->route('admin.dashboard');
        }

        $this->setTitleDescription(
            trans('admin/dashboard.titles.profile_list'),
            trans('admin/dashboard.descriptions.profile_list')
        );

        $tokenData = $this->settings->get('ga_token');

        if (!$tokenData) {
            return redirect()->route('admin.dashboard.authorization');
        }

        $token = (array)json_decode($tokenData);

        $client = \Google::getClient();
        $client->setAccessToken($token);
        $service = new \Google_Service_Analytics($client);

        $profilesList = [];
        $profiles = $service->management_profiles->listManagementProfiles('~all', '~all');

        foreach ($profiles->getItems() as $profile) {
            $profilesList[] = (object)[
                'id' => $profile->getId(),
                'accountId' => $profile->getAccountId(),
                'name' => $profile->getName(),
                'property' => $profile->getwebPropertyId(),
                'url' => $profile->getwebsiteUrl()
            ];
        }

        return view('admin.dashboard.choose_profile_list', compact('profilesList'));
    }


    /**
     * Save selected profile
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function saveSelectedProfile(Request $request)
    {
        if (!$this->canBeManagedByCurrentUser()) {
            flash()->warning(trans('admin/dashboard.notifications.no_ga_permission'));
            return response()->json([
                'redirect' => route('admin.dashboard')
            ]);
        }

        $profileId = $request->input('profileId');
        $accountId = $request->input('accountId');
        $propertyId = $request->input('propertyId');
        $enableTracking = intval($request->input('enableTracking', 0));

        $tokenData = $this->settings->get('ga_token');

        if (!$tokenData || !$accountId || !$propertyId || !$profileId) {
            flash(trans('admin/dashboard.notifications.ga_profile_not_selected'), 'warning');
            return $this->refresh();
        }

        $token = (array)json_decode($tokenData);

        $client = \Google::getClient();
        $client->setAccessToken($token);
        $service = new \Google_Service_Analytics($client);

        $profiles = $service->management_profiles->listManagementProfiles($accountId, $propertyId);

        $selectedProfile = null;
        foreach ($profiles->getItems() as $profile) {
            if ($profile->getId() === $profileId) {
                $selectedProfile = $profile;
                break;
            }
        }

        if (!$selectedProfile) {
            flash(trans('admin/dashboard.notifications.ga_profile_not_selected'), 'warning');
            return $this->refresh();
        }

        $this->settings->put('ga_profile_id', $profileId);
        $this->settings->put('ga_property_id', $propertyId);
        $this->settings->put('ga_enable_tracking', $enableTracking);

        return response()->json([
            'redirect' => route('admin.dashboard')
        ]);
    }


    /**
     * Log off from google analytics.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function logOff()
    {
        if (!$this->canBeManagedByCurrentUser()) {
            flash()->warning(trans('admin/dashboard.notifications.no_ga_permission'));
            return $this->refresh();
        }

        $this->settings->forget('ga_token');
        $this->settings->forget('ga_profile_id');
        $this->settings->forget('ga_property_id');
        $this->settings->forget('ga_enable_tracking');
        $this->settings->forget('ga_user_id');

        flash(trans('admin/dashboard.notifications.ga_disconnected'), 'success');
        return $this->refresh();
    }


    /**
     * Check if current dashboard configuration can be
     * managed by authenticated user.
     *
     * @return bool
     */
    private function canBeManagedByCurrentUser()
    {
        $gaUserId = (int)$this->settings->get('ga_user_id', 0);

        return auth()->user()->isAdmin() || $gaUserId === auth()->id();
    }
}
