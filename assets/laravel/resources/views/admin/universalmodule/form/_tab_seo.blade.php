<seo-inputs :title-placeholder="form.title"
            :description-placeholder="form.perex"
            :form="form"
            :trans="{{ \App\Helpers\Functions::combineTransToJson([
                'admin/general.seo', 'admin/article/form.seo_tab'
            ]) }}"
></seo-inputs>
