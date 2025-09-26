<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import assets from '@/routes/assets';
import { type BreadcrumbItem } from '@/types';

import ReusableDataTable from '@/components/entitycomponents/ReusableDataTable.vue'; // Table component for displaying data
import ReusableDropDownAction from '@/components/entitycomponents/ReusableDropDownAction.vue'; // Dropdown for row actions (edit/delete)
import { AutoForm } from '@/components/ui/auto-form'; // AutoForm component for form handling
import { Button } from '@/components/ui/button'; // Button component
import { Checkbox } from '@/components/ui/checkbox'; // Checkbox component for row selection
import {
    Dialog,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogScrollContent,
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
import { parseDate } from '@internationalized/date';

const baseentityurl = assets.index().url; // API endpoint for the entity
const baseentityname = 'Asset'; // Name of the entity

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: baseentityname,
        href: baseentityurl,
    },
];

const props = defineProps({
    categories: {
        type: Object,
        required: true,
    },
    locations: {
        type: Object,
        required: true,
    },
    manufacturers: {
        type: Object,
        required: true,
    },
    users: {
        type: Object,
        required: true,
    },
});

/* Define Props */
export interface Asset {
    id: number;
    category: any;
    category_id: string;
    location: any;
    location_id: string;
    manufacturer: any;
    manufacturer_id: string;
    assigned_to: any;
    assigned_to_user_id: string;
    asset_tag: string;
    name: string;
    serial_number: string;
    model_name: string;
    purchase_date: string;
    purchase_price: number;
    status: string;
    notes: string;
    assignedToUser: any;
}

/* Define Table Columns */
const columns: ColumnDef<Asset>[] = [
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
        enableSorting: false,
        enableHiding: false,
    },
    {
        accessorKey: 'asset_tag',
        header: ({ column }) =>
            h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () =>
                        column.toggleSorting(column.getIsSorted() === 'asc'),
                },
                () => ['Asset Tag', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
            ),
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.getValue('asset_tag'),
            ),
    },
    {
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.getValue('name'),
            ),
    },
    {
        accessorKey: 'serial_number',
        header: 'Serial Number',
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.getValue('serial_number'),
            ),
    },
    {
        accessorKey: 'model_name',
        header: 'Model Name',
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.getValue('model_name'),
            ),
    },
    {
        accessorKey: 'purchase_date',
        header: 'Purchase Date',
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.getValue('purchase_date'),
            ),
    },
    {
        accessorKey: 'purchase_price',
        header: 'Purchase Price',
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.getValue('purchase_price'),
            ),
    },
    {
        accessorKey: 'status',
        header: 'Status',
        cell: ({ row }) => {
            const status = row.getValue('status');

            // Map backend enum values to labels & colors
            const statusMap: Record<string, { label: string; class: string }> =
                {
                    Deployed: {
                        label: 'Deployed',
                        class: 'bg-green-100 text-green-800',
                    },
                    'In Storage': {
                        label: 'In Storage',
                        class: 'bg-blue-100 text-blue-800',
                    },
                    Maintenance: {
                        label: 'Maintenance',
                        class: 'bg-yellow-100 text-yellow-800',
                    },
                    Retired: {
                        label: 'Retired',
                        class: 'bg-gray-200 text-gray-800',
                    },
                    Broken: {
                        label: 'Broken',
                        class: 'bg-red-100 text-red-800',
                    },
                };

            const mapped = statusMap[status as string] ?? {
                label: status,
                class: 'bg-gray-100 text-gray-800',
            };

            return h(
                'span',
                {
                    class: `px-2 py-1 rounded text-xs font-medium ${mapped.class}`,
                },
                mapped.label,
            );
        },
    },
    {
        accessorKey: 'notes',
        header: 'Notes',
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal max-w-xs truncate' },
                row.getValue('notes') ?? 'no data',
            ),
    },
    {
        accessorKey: 'category.name',
        header: 'Category',
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.original.category.name,
            ),
    },
    {
        accessorKey: 'manufacturer_id',
        header: 'Manufacturer',
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.original.manufacturer.name ?? 'no data',
            ),
    },
    {
        accessorKey: 'location_id',
        header: 'Location',
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.original.location?.name ?? 'no data',
            ),
    },
    {
        accessorKey: 'assigned_to_user_id',
        header: 'Assigned To',
        cell: ({ row }) =>
            h(
                'div',
                { class: 'break-words whitespace-normal' },
                row.original.assignedToUser?.name ?? 'not assigned',
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

const statusEnum = {
    Deployed: 'Deployed',
    InStorage: 'In Storage',
    Maintenance: 'Maintenance',
    Retired: 'Retired',
    Broken: 'Broken',
};

/* Form Schema and Configuration */
const schema = z.object({
    asset_tag: z.string().min(1, { message: 'Asset Tag is required' }),
    name: z
        .string()
        .min(3, { message: 'Name must be at least 3 characters long' }),
    serial_number: z.string().nullable().optional(),
    model_name: z.string().nullable().optional(),
    purchase_date: z.coerce.date().nullable().optional(),
    purchase_price: z.coerce
        .number({
            invalid_type_error: 'Purchase price must be a number',
        })
        .nonnegative('Purchase price must be a positive number')
        .optional(),
    status: z.enum(Object.values(statusEnum) as [string, ...string[]], {
        required_error: 'Status is required',
    }),
    notes: z
        .string({
            invalid_type_error: 'Notes must be a string',
        })
        .max(1000, 'Notes must not exceed 1000 characters')
        .nullable()
        .optional(),
    category_id: z.enum(
        props.categories.map((item: any) => item.name),
        {
            required_error: 'Category is required',
        },
    ),
    manufacturer_id: z
        .enum(props.manufacturers.map((item: any) => item.name))
        .nullable()
        .optional(),
    location_id: z
        .enum(props.locations.map((item: any) => item.name))
        .nullable()
        .optional(),
    assigned_to_user_id: z
        .enum(props.users.map((item: any) => item.name))
        .nullable()
        .optional(),
});

const fieldconfig: any = {
    name: {
        label: 'Name',
        inputProps: {
            type: 'text',
            class: 'uppercase',
            placeholder: 'Enter asset name',
        },
    },
    serial_number: {
        label: 'Serial Number',
        inputProps: {
            type: 'text',
            class: 'uppercase',
            placeholder: 'Enter serial number',
        },
    },
    model_name: {
        label: 'Model Name',
        inputProps: {
            type: 'text',
            class: 'uppercase',
            placeholder: 'Enter model name',
        },
    },
    category_id: {
        label: 'Select Category', // Custom label for the field
        component: 'select', // Tell AutoForm to render a <select> element
        inputProps: {
            placeholder: 'Choose a category...', // Placeholder for the select
        },
    },
    location_id: {
        label: 'Select Location', // Custom label for the field
        component: 'select', // Tell AutoForm to render a <select> element
        inputProps: {
            placeholder: 'Choose a location...', // Placeholder for the select
        },
    },
    manufacturer_id: {
        label: 'Select Manufacturer', // Custom label for the field
        component: 'select', // Tell AutoForm to render a <select> element
        inputProps: {
            placeholder: 'Choose a manufacturer...', // Placeholder for the select
        },
    },
    assigned_to_user_id: {
        label: 'Select Assigned User', // Custom label for the field
        component: 'select', // Tell AutoForm to render a <select> element
        inputProps: {
            placeholder: 'Choose a user...', // Placeholder for the select
        },
    },
    asset_tag: {
        label: 'Asset Tag',
        inputProps: {
            type: 'text',
            class: 'uppercase',
            placeholder: 'Enter asset tag',
        },
    },
    purchase_date: {
        label: 'Purchase Date',
        inputProps: {
            type: 'date',
        },
        description: 'Date when the asset was purchased.',
    },
    purchase_price: {
        label: 'Purchase Price',
        inputProps: {
            type: 'number',
            step: '0.01', // Allows 2 decimal places
            placeholder: 'Enter purchase price',
        },
    },
    status: {
        label: 'Status',
        component: 'select', // Dropdown for selecting status
    },
    notes: {
        label: 'Notes',
        component: 'textarea', // Textarea for notes
        inputProps: {
            placeholder: 'Enter additional notes',
        },
    },
};
const form = useForm({
    validationSchema: toTypedSchema(schema), // Validation schema
    initialValues: {
        asset_tag: '',
        assigned_to_user_id: null,
        category_id: null,
        location_id: null,
        manufacturer_id: null,
        model_name: '',
        name: '',
        notes: '',
        purchase_date: null,
        purchase_price: 0,
        serial_number: '',
        status: '',
    },
});

const resetForm = () => {
    form.resetForm();
    itemID.value = null;
};

const tableRef = ref<InstanceType<typeof ReusableDataTable> | null>(null);

/**
SUBMIT CREATE| UPDATE | DELETE
*/
const onSubmit = async (values: any) => {
    try {
        const mappedValues = {
            ...values,
            category_id:
                props.categories.find(
                    (category: any) => category.name === values.category_id,
                )?.id || null,
            location_id:
                props.locations.find(
                    (location: any) => location.name === values.location_id,
                )?.id || null,
            manufacturer_id:
                props.manufacturers.find(
                    (manufacturer: any) =>
                        manufacturer.name === values.manufacturer_id,
                )?.id || null,
            assigned_to_user_id:
                props.users.find(
                    (user: any) => user.name === values.assigned_to_user_id,
                )?.id || null,
        };

        if (mode.value === 'create') {
            await axios.post(`${baseentityurl}`, mappedValues); // Create a new category
            toast.success(`${baseentityname} created successfully.`);
        } else if (mode.value === 'edit') {
            await axios.put(`${baseentityurl}/${itemID.value}`, mappedValues); // Update an existing category
            toast.success(`${baseentityname} updated successfully.`);
        }

        resetForm(); // Reset the form
        await tableRef.value?.fetchRows(); // Refresh the table data
        showDialogForm.value = false; // Hide the form dialog
    } catch (error: any) {
        if (error.response && error.response.status === 422) {
            form.setErrors(error.response.data.errors); // Set validation errors
            toast.error('Validation failed. Please check your input.');
        } else {
            toast.error('An unexpected error occurred.');
        }
    }
};

/**
 Edit modal
  */
const handleEdit = async (id: number) => {
    try {
        mode.value = 'edit'; // Set mode to edit
        itemID.value = id; // Set the item ID
        const response = await axios.get(`${baseentityurl}/${id}`); // Fetch the item data
        const data = response.data;
        console.log('response', response.data);
        const mappedData = {
            ...data,
            category_id: data.category?.name,
            location_id: data.location?.name,
            manufacturer_id: data.manufacturer?.name,
            assigned_to_user_id: data.assigned_to_user?.name,
            purchase_date: data.purchase_date ? parseDate(data.purchase_date.slice(0, 10)) : null,
        };
        // const testdate = parseAbsolute(data.purchase_date, getLocalTimeZone())
        // console.log(df.format(testdate.toDate(getLocalTimeZone())))
        form.setValues(mappedData); // Populate the form with the item data
        showDialogForm.value = true; // Show the form dialog
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
            <h1 class="text-2xl font-bold">Asset Page</h1>
            <p class="max-w-3xl">
                This is an asset page. You can add your asset related content
                here.
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
                <DialogScrollContent class="sm:max-w-[425px]">
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
                </DialogScrollContent>
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
