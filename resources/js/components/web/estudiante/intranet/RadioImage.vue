<template>
    <label :for="link">
        <img :src="src" :style="[isChecked ? { filter: 'grayscale(0)', width: '40px' } : { filter: 'grayscale(1)', width: '35px' }, { margin: '0 10px' }]" alt="" />
        <input v-show="false" type="radio" :checked="isChecked" :id="link" :value="value" @change="$emit('change', $event.target.value)" />
    </label>
</template>

<script>
export default {
    model: {
        prop: "modelValue",
        event: "change"
    },
    props: {
        src: { type: String, default: "", required: true },
        link: { type: String, default: "", required: true },
        modelValue: { default: "" },
        value: { type: String, default: undefined }
    },
    computed: {
        isChecked() {
            return this.modelValue == this.value;
        }
    }
};
</script>

<style lang="scss" scoped></style>
