<template>
    <div class="element-lock-hover" v-show="isLocked">
        <div class="lock-inner">
            <i class="fa fa-spinner fa-spin"></i>
            <div v-if="text">
                <strong>{{text}}</strong>
            </div>
        </div>
    </div>
</template>

<script>
	export default {
        props: {
			isLocked: {
				type: Boolean,
                'default': true
            },
            text: {
				type: String,
                'default': null
            }
        }
	}
</script>
