<script>
  import { ref, watch } from 'vue';

  export default {
    name: 'AppForm',

    props: {
      initialData: {
        type: Object,
        default: null,
      },
      onSubmit: {
        type: Function,
        required: true
      },
    },

    setup(props) {
      const form = ref({ ...props.initialData });

      const showError = ref('');
      const submitButtonText = ref('');

      if (props.initialData) {
        submitButtonText.value = 'Update App';
      } else {
        submitButtonText.value = 'Create App';
      }

      watch(() => props.initialData, (val) => {
        if (val) {
          form.value = { 
            ...val, 
            active: !!val?.active || false,
          }
        }
        
      }, { immediate: true });

      const handleSubmit = () => {
        if (props.onSubmit) {
          props.onSubmit(form.value, (errorMsg) => {
            if (errorMsg == 422) {
              showError.value = 'App already exists';
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
    <!-- name -->
    <div class="form-group">
      <label for="name">App Name <span class="required">*</span></label>
      <input
        v-model="form.name"
        id="name"
        name="name"
        class="form-control"
        required
      />
    </div>

    <!-- url -->
    <div class="form-group">
      <label for="url">App URL <span class="required">*</span></label>
      <input
        v-model="form.url"
        id="url"
        name="url"
        type="url"
        class="form-control"
        required
      />
    </div>

    <!-- active -->
    <div class="form-group checkbox-group">
      <label>Active</label>

      <input
          type="checkbox"
          v-model="form.active"
          name="active"
      />
    </div>

    <!-- error -->
    <span v-if="showError" class="form-validation-error">
      {{ showError }}
    </span>

    <div class="form-footer">
      <button type="submit" class="btn btn-primary">
        {{ submitButtonText }}
      </button>
    </div>
  </form>
</template>
