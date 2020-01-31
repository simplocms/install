import Form from './vue-components/form/form';
import FormGroup from './vue-components/form/form-group';
import CheckboxSwitch from './vue-components/form/checkbox-switch';
import Radio from './vue-components/form/radio';

Vue.component('v-form', Form);
Vue.component('v-form-group', FormGroup);
Vue.component('v-checkbox-switch', CheckboxSwitch);
Vue.component('v-radio', Radio);

Vue.component('v-datatable', require('./vue-components/datatable/datatable'));
Vue.component('v-automatic-post', require('./vue-components/automatic-post'));

require('./media-library/index');
