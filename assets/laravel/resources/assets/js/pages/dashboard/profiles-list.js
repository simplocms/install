const options = window.dashboardProfilesListOptions();

Vue.component('dashboard-profiles-list', {
    data () {
        return {
            enableTracking: true
        }
    },

    methods: {
        selectProfile (accountId, propertyId, profileId) {
            var $list = $(this.$el);

            if (!$list.lock({ spinner: SpinnerType.OVER })) {
                return false;
            }

            Request.post(options.submitUrl, {
                accountId: accountId,
                propertyId: propertyId,
                profileId: profileId,
                enableTracking: this.enableTracking * 1
            }).done(function (response) {
                if (response.refresh) {
                    window.location.reload(true);
                } else if (response.redirect) {
                    window.location = response.redirect;
                }
            }).always(function () {
                $list.unlock();
            });
        }
    }
});