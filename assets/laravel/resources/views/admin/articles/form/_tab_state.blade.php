<publishing-state-inputs :publishing-states="{{ $publishingStates }}"
                         :form="form"
                         :trans="{{ json_encode(trans('admin/general.publishing_states_component')) }}"
></publishing-state-inputs>

@if($article->exists)
<a href="{{ $article->full_url }}" target="_blank" class="btn bg-teal-400 mt-10">
    {{ trans('admin/article/form.btn_preview') }}
</a>
@endif
