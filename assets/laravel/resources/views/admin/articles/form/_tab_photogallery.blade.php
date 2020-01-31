<photogallery ref="photogallery"
              :photos="{{ isset($photos) ? $photos->toJson() : '[]' }}"
              :trans="{{ json_encode(trans('admin/general.photogallery')) }}"
></photogallery>
