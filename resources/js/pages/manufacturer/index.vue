<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import manufacturers from '@/routes/manufacturers';
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
const baseentityurl = manufacturers.index().url; // API endpoint for the entity
const baseentityname = 'Manufacturer'; // Name of the entity

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: baseentityname,
        href: baseentityurl,
    },
];

/* Define Props */
export interface Manufacturer {
    id: number;
    name: string;
    url: string;
    support_url: string;
    support_email: string;
    support_phone: string;
}

/* Define Table Columns */
const columns: ColumnDef<Manufacturer>[] = [
    {
        id: 'select',
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
        enableSorting: false,
        enableHiding: false,
    },
    {
        accessorKey: 'name',
        header: ({ column }) =>
            h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () =>
                        column.toggleSorting(column.getIsSorted() === 'asc'),
                },
                () => ['Name', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
            ),
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.getValue('name'),
            ),
    },
    {
        accessorKey: 'url',
        header: 'URL',
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.getValue('url'),
            ),
    },
    {
        accessorKey: 'support_url',
        header: 'Support URL',
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.getValue('support_url'),
            ),
    },
    {
        accessorKey: 'support_phone',
        header: 'Support Phone',
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.getValue('support_phone'),
            ),
    },
    {
        accessorKey: 'support_email',
        header: 'Email',
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.getValue('support_email'),
            ),
    },
    {
        id: 'actions',
        enableHiding: false,
        cell: ({ row }) => {
            const rowitem = row.original;
            return h(
                'div',
                { class: 'relative' },
                h(ReusableDropDownAction, {
                    rowitem,
                    onEdit: handleEdit,
                    onDelete: openDeleteDialog,
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
    url: z.string().nullable().default(''),            // allow null or empty
    support_url: z.string().nullable().default(''),    // allow null or empty
    support_phone: z.string().nullable().default(''),  // allow null or empty
    support_email: z
        .string()
        .email('Invalid email')
        .nullable()
        .default(''), // optional email with default ''
});

const fieldconfig: any = {
    name: {
        label: 'Name',
        inputProps: {
            type: 'text',
            placeholder: 'Enter name',
        },
    },
    url: {
        label: 'URL',
        inputProps: {
            type: 'text',
            placeholder: 'Enter URL',
        },
    },
    support_url: {
        label: 'Support URL',
        inputProps: {
            type: 'text',
            placeholder: 'Enter support URL',
        },
    },
    support_phone: {
        label: 'Support Phone',
        inputProps: {
            type: 'text',
            placeholder: 'Enter support phone',
        },
    },
    support_email: {
        label: 'Support Email',
        inputProps: {
            type: 'email',
            placeholder: 'Enter support email',
        },
    },
};

const form = useForm({
    validationSchema: toTypedSchema(schema), // Validation schema
    initialValues: {
        name: '',
        url: '',
        support_url: '',
        support_phone: '',
        support_email: '',
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
            <h1 class="text-2xl font-bold">Manufacturer Page</h1>
            <p class="max-w-3xl">
                This is a manufacturer page. You can add your manufacturer
                related content here.
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
                :baseentityurl="baseentityurl"
            />

            <!-- Dialog Form -->
            <Dialog v-model:open="showDialogForm">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle
                            >{{ mode === 'create' ? 'Create' : 'Update' }}
                            {{ baseentityname }}</DialogTitle
                        >
                    </DialogHeader>
                    <DialogDescription>
                        Use this form to edit the {{ baseentityname }} details.
                    </DialogDescription>
                    <AutoForm
                        class="space-y-6"
                        :form="form"
                        :schema="schema"
                        :field-config="fieldconfig"
                        @submit="onSubmit"
                    >
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
