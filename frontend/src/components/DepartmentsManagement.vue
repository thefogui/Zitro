<script>
  import { ref, onMounted } from 'vue';
  import EasyDataTable from 'vue3-easy-data-table';
  import 'vue3-easy-data-table/dist/style.css';
  import { getDepartments, getDepartment, createDepartment, updateDepartment, deleteDepartment } from '../services/departmentService';
  import DepartmentForm from './forms/DepartmentForm.vue';

  export default {
    
    name: 'DepartmentsManagement',
    components: { EasyDataTable, DepartmentForm },

    setup() {
      const departments = ref([]);
      const headers = [
        { text: 'ID', value: 'id' },
        { text: 'Department Name', value: 'name' },
        { text: 'Actions', value: 'actions' },
      ];

      const showDepartmentForm = ref(false);
      const editingDepartment = ref(null);

      const fetchDepartments = async () => {
        try {
          departments.value = await getDepartments();
        } catch (err) {
          console.error('Error fetching departments:', err);
        }
      };

      const openDepartmentForm = () => {
        showDepartmentForm.value = true;
      };

      const closeDepartmentForm = () => {
        showDepartmentForm.value = false;
        editingDepartment.value = null;
        fetchDepartments();
      };

      const handleSubmit = async (formData, setError) => {
        try {
          if (editingDepartment.value) {
            await updateDepartment(editingDepartment.value.id, formData);
          } else {
            await createDepartment(formData);
          }
          closeDepartmentForm();
        } catch (err) {
          setError(err.message);
        }
      };

      const handleDeleteDepartment = async (department) => {
        await deleteDepartment(department.id);
        fetchDepartments();
      };

      const editDepartment = async (department) => {
        try {
          department = await getDepartment(department.id);
          editingDepartment.value = { ...department };
          openDepartmentForm(department);
        } catch (err) {
          console.error('Error fetching department:', err);
        }
      };

      onMounted(fetchDepartments);

      return {
        departments,
        headers,
        handleDeleteDepartment,
        editDepartment,
        showDepartmentForm,
        openDepartmentForm,
        closeDepartmentForm,
        handleSubmit,
        editingDepartment,
      };
    },
  };
</script>

<template>
  <div class="table-container">
    <div class="create-new-button-container">
      <button class="btn btn-primary" @click="openDepartmentForm">New Department</button>
    </div>

    <EasyDataTable
      :headers="headers"
      :items="departments"
      show-index
      border-cell
      alternating
    >
      <template #item-actions="item">
        <div class="action-buttons">
          <button class="btn btn-primary" @click="editDepartment(item)">Edit</button>
          <button class="btn btn-danger" @click="handleDeleteDepartment(item)">Delete</button>
        </div>
      </template>
    </EasyDataTable>

    <div v-if="showDepartmentForm" class="modal-overlay">
      <div class="modal-content">
        <button class="close-btn" @click="closeDepartmentForm">X</button>

        <DepartmentForm
          :initialData="editingDepartment"
          :onSubmit="handleSubmit"
        />
      </div>
    </div>
  </div>
</template>
