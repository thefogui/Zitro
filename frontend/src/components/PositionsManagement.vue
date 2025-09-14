<script>
  import { ref, onMounted } from 'vue';
  import EasyDataTable from 'vue3-easy-data-table';
  import 'vue3-easy-data-table/dist/style.css';
  import { getPositions, getPosition, createPosition, updatePosition, deletePosition } from '../services/companyPositionService';
  import PositionForm from './forms/PositionForm.vue';

  export default {
    name: 'PositionsManagement',
    components: { EasyDataTable, PositionForm },
    setup() {
      const positions = ref([]);
      const headers = [
        { text: 'ID', value: 'id' },
        { text: 'Name', value: 'name' },
        { text: 'Actions', value: 'actions' },
      ];

      const showPositionForm = ref(false);
      const editingPosition = ref(null);

      const fetchPositions = async () => {
        try {
          positions.value = await getPositions();
        } catch (err) {
          console.error('Error fetching positions:', err);
        }
      };

      const openPositionForm = (position = null) => {
        editingPosition.value = position;
        showPositionForm.value = true;
      };

      const closePositionForm = () => {
        showPositionForm.value = false;
        fetchPositions();
      };

      const handleSubmit = async (formData, setError) => {
        try {
          if (editingPosition.value) {
            await updatePosition(editingPosition.value.id, formData);
          } else {
            await createPosition(formData);
          }
          closePositionForm();
        } catch (err) {
          setError(err.message);
        }
      };

      const handleDeletePosition = async (position) => {
        await deletePosition(position.id);
        fetchPositions();
      };

      const editPosition = async (position) => {
        try {
          position = await getPosition(position.id);
          editingPosition.value = { ...position };
          openPositionForm(position);
        } catch (err) {
          console.error('Error fetching Position:', err);
        }
       
      };

      onMounted(fetchPositions);

      return {
        positions,
        headers,
        handleDeletePosition,
        editPosition,
        showPositionForm,
        openPositionForm,
        closePositionForm,
        handleSubmit,
        editingPosition,
      };
    },
  };
</script>

<template>
  <div class="table-container">
    <div class="create-new-button-container">
      <button class="btn btn-primary" @click="openPositionForm()">New Position</button>
    </div>

    <EasyDataTable
      :headers="headers"
      :items="positions"
      show-index
      border-cell
      alternating
    >
      <template #item-actions="item">
        <div class="action-buttons">
          <button class="btn btn-primary" @click="editPosition(item)">Edit</button>
          <button class="btn btn-danger" @click="handleDeletePosition(item)">Delete</button>
        </div>
      </template>
    </EasyDataTable>

    <div v-if="showPositionForm" class="modal-overlay">
      <div class="modal-content">
        <button class="close-btn" @click="closePositionForm">X</button>

        <PositionForm
          :isCreate="!editingPosition"
          :initialData="editingPosition || {}"
          :onSubmit="handleSubmit"
        />
      </div>
    </div>
  </div>
</template>
