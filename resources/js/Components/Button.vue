<script setup>
import PrimaryButton from './PrimaryButton.vue';
import SecondaryButton from './SecondaryButton.vue';
import DangerButton from './DangerButton.vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'danger', 'outline'].includes(value)
    }
});

const componentMap = {
    primary: PrimaryButton,
    secondary: SecondaryButton,
    danger: DangerButton,
    outline: SecondaryButton
};
</script>

<template>
    <component
        :is="componentMap[variant]"
        v-bind="$attrs"
        :class="{
            'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50': variant === 'outline'
        }"
    >
        <slot />
    </component>
</template> 