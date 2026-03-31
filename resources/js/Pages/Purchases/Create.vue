<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    purchase: Object,
    suppliers: Array
});

const isEdit = !!props.purchase;

const form = useForm({
    supplier_id: props.purchase ? props.purchase.supplier_id : '',
    total_amount: props.purchase ? props.purchase.total_amount : '',
    purchase_date: props.purchase ? props.purchase.purchase_date : '',
    
});

const submit = () => {
    if (isEdit) {
        form.put(route('purchases.update', props.purchase.id));
    } else {
        form.post(route('purchases.store'));
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Purchase' : 'Create Purchase'" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ isEdit ? 'Edit Purchase' : 'Create Purchase' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit">
                                                    <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Supplier</label>
                            <select v-model="form.supplier_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700">
                                <option v-for="relItem in suppliers" :key="relItem.id" :value="relItem.id">{{ relItem.name || relItem.id }}</option>
                            </select>
                            <div v-if="form.errors.supplier_id" class="text-red-500 text-xs mt-1">{{ form.errors.supplier_id }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Total_amount</label>
                            <input type="number" v-model="form.total_amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700">
                            <div v-if="form.errors.total_amount" class="text-red-500 text-xs mt-1">{{ form.errors.total_amount }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Purchase_date</label>
                            <input type="text" v-model="form.purchase_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700">
                            <div v-if="form.errors.purchase_date" class="text-red-500 text-xs mt-1">{{ form.errors.purchase_date }}</div>
                        </div>


                            <div class="flex items-center justify-end">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition ease-in-out duration-150">
                                    {{ isEdit ? 'Update' : 'Create' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>