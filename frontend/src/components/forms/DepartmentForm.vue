<script>
  import { ref, watch } from 'vue';

  export default {
    name: 'DepartmentForm',
    props: { 
      initialData: {
        type: Object,
        default: null,
      },
      onSubmit: {
        type: Function,
        required: true,
      },
    },
    setup(props) {
      const form = ref({ ...props.initialData });
      const showError = ref('');
      const submitButtonText = ref(props.initialData ? 'Update Department' : 'Create Department');

      watch(() => props.initialData, (val) => {
        if (val) {
          form.value = { ...val };
        }
      }, { immediate: true });

      const handleSubmit = () => {
        if (props.onSubmit) {
          props.onSubmit(form.value, (errorMsg) => {
            if (errorMsg == 422) {
              showError.value = 'Department already exists';
            } else {
              showError.value = 'There was an error';
            }
          });
        }
      };

      return { form, handleSubmit, showError, submitButtonText };
    },
  };
</script>

<template>
  <form @submit.prevent="handleSubmit">
    <div class="form-group">
      <label for="name">Department Name <span class="required">*</span></label>
      <input
        v-model="form.name"
        id="name"
        name="name"
        class="form-control"
        required
      />
    </div>

    <span v-if="showError" class="form-validation-error">{{ showError }}</span>

    <div class="form-footer">
      <button type="submit" class="btn btn-primary">{{ submitButtonText }}</button>
    </div>
  </form>
</template>
