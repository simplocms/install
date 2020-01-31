@foreach (session('flash_notification', collect())->toArray() as $message)
$.jGrowl('{{ $message['message'] }}', {
header: '@if(!empty($message['title'])) {{ $message['title'] }} @elseif("danger" == $message['level']) {{ trans('admin/general.flash_level.danger') }} @elseif("success" == $message['level']) {{ trans('admin/general.flash_level.success') }} @elseif("warning" == $message['level']) {{ trans('admin/general.flash_level.warning') }} @else {{ trans('admin/general.flash_level.info') }} @endif',
theme: '@if("danger" == $message['level']) bg-danger @elseif("success" == $message['level']) bg-teal @elseif("warning" == $message['level']) bg-warning @elseif("info" == $message['level']) bg-info @else bg-slate-400 @endif alert-styled-left alert-styled-custom-{{ $message['level'] }}'
});
@endforeach
{{ session()->forget('flash_notification') }}
