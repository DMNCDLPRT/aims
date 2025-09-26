<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import locations from '@/routes/locations';
import { type BreadcrumbItem } from '@/types';

import ReusableDataTable from '@/components/entitycomponents/ReusableDataTable.vue'; // Table component for displaying data
import ReusableDropDownAction from '@/components/entitycomponents/ReusableDropDownAction.vue'; // Dropdown for row actions (edit/delete)
import { AutoForm } from '@/components/ui/auto-form'; // AutoForm component for form handling
import { Button } from '@/components/ui/button'; // Button component
import { Checkbox } from '@/components/ui/checkbox'; // Checkbox component for row selection
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'; // Dialog components for forms

/* Import Utilities */
import { toTypedSchema } from '@vee-validate/zod'; // Utility for converting Zod schemas to Vee-Validate schemas
import { ArrowUpDown, Plus } from 'lucide-vue-next'; // Icons for UI
import { useForm } from 'vee-validate'; // Form validation library
import { h, ref } from 'vue'; // Vue composition API utilities
import * as z from 'zod'; // Zod library for schema validation

/* Import Table Utilities */
import type { ColumnDef } from '@tanstack/vue-table'; // Type definitions for table columns

/* Import Types */
import ReusableAlertDialog from '@/components/entitycomponents/ReusableAlertDialog.vue';
import axios from 'axios';
import { toast } from 'vue-sonner';

/* Base Entity Configuration */
const baseentityurl = locations.index().url; // API endpoint for the entity
const baseentityname = 'Location'; // Name of the entity

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: baseentityname,
        href: baseentityurl,
    },
];

/* Define Props */
export interface Location {
    id: number; // Unique identifier for the category
    name: string; // Name of the category
    address: string; // Description of the category
}

/* Define Table Columns */
const columns: ColumnDef<Location>[] = [
    {
        id: 'select', // Column for row selection
        header: ({ table }) =>
            h(Checkbox, {
                modelValue:
                    table.getIsAllPageRowsSelected() ||
                    (table.getIsSomePageRowsSelected() && 'indeterminate'),
                'onUpdate:modelValue': (value) =>
                    table.toggleAllPageRowsSelected(!!value),
                ariaLabel: 'Select all',
            }),
        cell: ({ row }) =>
            h(Checkbox, {
                modelValue: row.getIsSelected(),
                'onUpdate:modelValue': (value) => row.toggleSelected(!!value),
                ariaLabel: 'Select row',
            }),
        enableSorting: false, // Disable sorting for this column
        enableHiding: false, // Disable hiding for this column
    },
    {
        accessorKey: 'name', // Column for category name
        header: ({ column }) => {
            return h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () =>
                        column.toggleSorting(column.getIsSorted() === 'asc'),
                },
                () => ['Name', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
            );
        },
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.getValue('name'),
            ),
    },
    {
        accessorKey: 'address', // Column for location address
        header: 'Address',
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.getValue('address'),
            ),
    },
    {
        id: 'actions', // Column for row actions (edit/delete)
        enableHiding: false, // Disable hiding for this column
        cell: ({ row }) => {
            const rowitem = row.original;

            return h(
                'div',
                { class: 'relative' },
                h(ReusableDropDownAction, {
                    rowitem,
                    onEdit: handleEdit, // Edit handler
                    onDelete: openDeleteDialog, // Delete handler
                }),
            );
        },
    },
];

/* Dialog State */
const showDialogForm = ref(false); // State for showing/hiding the form dialog
const mode = ref('create'); // Mode for the form (create/edit)
const itemID = ref<number | null>(null); // ID of the item being edited

const handleOpenDialogForm = () => {
    showDialogForm.value = true; // Show the form dialog
    mode.value = 'create'; // Set mode to create
};

/* Form Schema and Configuration */
const schema = z.object({
    name: z
        .string({
            required_error: 'Name is required',
            invalid_type_error: 'Name must be a string',
        })
        .min(3, {
            message: 'Name must be at least 3 characters long',
        }),
    address: z.string(),
});

const fieldconfig: any = {
    name: {
        label: 'Name',
        inputProps: {
            type: 'text',
        },
        address: 'Name of the address',
    },
    address: {
        label: 'Address',
        component: 'textarea',
        inputProps: {
            placeholder: 'Enter address',
        },
    },
};

const form = useForm({
    validationSchema: toTypedSchema(schema), // Validation schema
    initialValues: {
        name: '',
        address: '',
    },
});

const restForm = () => {
    form.resetForm();
    itemID.value = null;
};

const tableRef = ref<InstanceType<typeof ReusableDataTable> | null>(null);

/**
SUBMIT CREATE| UPDATE | DELETE
*/
const onSubmit = async (values: any) => {
    try {
        if (mode.value === 'create') {
            await axios.post(`${baseentityurl}`, values);
            toast.success(`${baseentityname} created successfully.`);
        } else if (mode.value === 'edit') {
            await axios.put(`${baseentityurl}/${itemID.value}`, values);
            toast.success(`${baseentityname} updated successfully`);
        }

        restForm();
        await tableRef.value?.fetchRows();
        showDialogForm.value = false;
    } catch (error: any) {
        if (error.response && error.response.status === 422) {
            form.setErrors(error.response.data.errors);
            toast.error('An unexpected error occurred');
        } else {
            toast.error('An unexpected error occurred');
        }
    }
};

/**
 Edit modal
  */
const handleEdit = async (id: number) => {
    try {
        mode.value = 'edit';
        itemID.value = id;
        const response = await axios.get(`${baseentityurl}/${id}`);
        console.log(response);
        form.setValues(response.data);
        showDialogForm.value = true;
    } catch (error) {
        console.log(`Error fetching ${baseentityname} data:`, error);
        toast.error(`Failed to fetch ${baseentityname} data.`);
    }
};

const showDeleteDialog = ref(false);
const itemIDToDelete = ref<number | null>(null);

const openDeleteDialog = (id: number) => {
    itemIDToDelete.value = id;
    showDeleteDialog.value = true;
};

/**
 *
 * Delete modal
 *
 */
const handleDelete = async () => {
    try {
        if (!itemIDToDelete.value) return;

        await axios.delete(`${baseentityurl}/${itemIDToDelete.value}`); // Delete the item
        toast.success(`${baseentityname} deleted successfully.`);
        await tableRef.value?.fetchRows(); // Refresh the table data
        showDeleteDialog.value = false; // Hide the delete confirmation dialog
    } catch (error) {
        console.log(`Error deleting ${baseentityname}:`, error);
        toast.error(`Failed to delete ${baseentityname}. Please try again.`);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <h1 class="text-2xl font-bold">Locations Page</h1>
            <p class="max-w-3xl">
                This is a locations page. You can add your locations related
                content here.
            </p>

            <div class="flex items-center gap-2 py-2">
                <div class="ml-auto flex items-center gap-2">
                    <Button class="bg-primary" @click="handleOpenDialogForm">
                        <Plus class="h-4"></Plus> Create {{ baseentityname }}
                    </Button>
                </div>
            </div>

            <!-- Table -->
            <ReusableDataTable
                ref="tableRef"
                :columns="columns"
                :baseentityname="baseentityname"
                :baseentityurl="baseentityurl"/>

            <!-- Dialog Form -->
            <Dialog v-model:open="showDialogForm">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle
                            >{{ mode === 'create' ? 'Create' : 'Update' }}
                            {{ baseentityname }}
                        </DialogTitle>
                    </DialogHeader>
                    <DialogDescription>
                        Use this form to edit the {{ baseentityname }} details.
                    </DialogDescription>
                    <AutoForm
                        class="space-y-6"
                        :form="form"
                        :schema="schema"
                        :field-config="fieldconfig"
                        @submit="onSubmit">
                        <DialogFooter>
                            <Button type="submit" class="bg-primary">
                                {{ mode === 'create' ? 'Create' : 'Update' }}
                            </Button>
                        </DialogFooter>
                    </AutoForm>
                </DialogContent>
            </Dialog>

            <ReusableAlertDialog
                :open="showDeleteDialog"
                :title="`Delete ${baseentityname}`"
                :description="`Are you sure you want t delete this ${baseentityname}? this action cannot be undone.`"
                @confirm="handleDelete"
                @cancel="showDeleteDialog = false"
            />
        </div>
    </AppLayout>
</template>
