!function(t){var i={};function a(e){if(i[e])return i[e].exports;var o=i[e]={i:e,l:!1,exports:{}};return t[e].call(o.exports,o,o.exports,a),o.l=!0,o.exports}a.m=t,a.c=i,a.d=function(t,i,e){a.o(t,i)||Object.defineProperty(t,i,{configurable:!1,enumerable:!0,get:e})},a.n=function(t){var i=t&&t.__esModule?function(){return t.default}:function(){return t};return a.d(i,"a",i),i},a.o=function(t,i){return Object.prototype.hasOwnProperty.call(t,i)},a.p="",a(a.s=0)}({0:function(t,i,a){t.exports=a("DMKW")},DMKW:function(t,i,a){"use strict";var e=Object.assign||function(t){for(var i=1;i<arguments.length;i++){var a=arguments[i];for(var e in a)Object.prototype.hasOwnProperty.call(a,e)&&(t[e]=a[e])}return t},o=window.imageModuleOptions();new Vue({el:"#m-image-configuration-form",data:{localization:new Localization(window.cms_trans),formData:e({},o.model),$form:null,image:null},mounted:function(){this.$form=$(this.$el),this.$form.trigger("admin:form-fill-ready",this.fillForm),this.$form.on("admin:form-submit-data",this.getFormData)},beforeDestroy:function(){this.$form.off("admin:form-submit-data",this.getFormData)},computed:{aspectRatio:function(){return this.isResolutionAvailable?this.image.getWidth()/this.image.getHeight():1},isResolutionAvailable:function(){return this.image&&this.image.isResolutionAvailable()}},watch:{image:function(t){t&&t.isResolutionAvailable()&&(t.getId()!==this.formData.image_id||null===this.formData.width)&&(this.formData.width=t.getWidth(),this.formData.height=t.getHeight()),t&&null===this.formData.alt&&(this.formData.alt=t.getDescription())}},methods:{fillForm:function(t){this.formData=t,this.image=t._temp&&t._temp.image||null},getFormData:function(t,i){i.alt=this.formData.alt,i.img_class=this.formData.img_class,i.is_sized=this.formData.is_sized,i.image_id=this.image?this.image.getId():null,this.formData.is_sized&&(i.width=Number(this.formData.width),i.height=Number(this.formData.height)),i._temp={image:this.image}},widthChanged:function(t){this.isResolutionAvailable&&(t.target.value>this.image.getWidth()&&(this.formData.width=this.image.getWidth()),this.formData.height=Math.round(this.formData.width/this.aspectRatio))},heightChanged:function(t){this.isResolutionAvailable&&(t.target.value>this.image.getHeight()&&(this.formData.height=this.image.getHeight()),this.formData.width=Math.round(this.formData.height*this.aspectRatio))}}})}});