<script>
  import { ref, watch } from 'vue';

  export default {
    name: 'PositionForm',

    props: {
      isCreate: {
        type: Boolean,
        default: true,
        required: true,
      },
      initialData: {
        type: Object,
        default: () => ({ name: '' }),
      },
      onSubmit: {
        type: Function,
        required: true,
      },
    },
    
    setup(props) {
      const form = ref({ ...props.initialData });
      const showError = ref('');
      const submitButtonText = ref(props.isCreate ? 'Create Position' : 'Update Position');

      watch(() => props.initialData, (val) => {
        form.value = { ...val };
      });

      const handleSubmit = () => {
        if (props.onSubmit) {
          props.onSubmit(form.value, (errorMsg) => {
            if (errorMsg == 422) {
              showError.value = 'Position already exists';
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
      <label for="name">Position Name <span class="required">*</span></label>
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
