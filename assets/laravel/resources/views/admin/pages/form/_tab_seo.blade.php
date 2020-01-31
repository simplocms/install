<seo-inputs :title-placeholder="form.name"
            :form="form"
            :show-index="!isTestingCounterpart"
            :show-follow="!isTestingCounterpart"
            :show-sitemap="!isTestingCounterpart"
            :trans="{{ \App\Helpers\Functions::combineTransToJson([
                'admin/general.seo', 'admin/pages/form.seo_tab'
            ]) }}"
></seo-inputs>
