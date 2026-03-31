<?php

$resources = [
    'Category' => [
        'plural' => 'categories',
        'fields' => ['name' => 'string', 'description' => 'text'],
        'has_relations' => false,
    ],
    'Supplier' => [
        'plural' => 'suppliers',
        'fields' => ['name' => 'string', 'email' => 'string', 'phone' => 'string', 'address' => 'text'],
        'has_relations' => false,
    ],
    'Customer' => [
        'plural' => 'customers',
        'fields' => ['name' => 'string', 'email' => 'string', 'phone' => 'string', 'address' => 'text'],
        'has_relations' => false,
    ],
    'Product' => [
        'plural' => 'products',
        'fields' => ['category_id' => 'integer', 'name' => 'string', 'code' => 'string', 'description' => 'text', 'price' => 'decimal', 'stock' => 'integer'],
        'has_relations' => true,
        'relations' => ['Category' => 'categories']
    ],
    'Purchase' => [
        'plural' => 'purchases',
        'fields' => ['supplier_id' => 'integer', 'total_amount' => 'decimal', 'purchase_date' => 'string'],
        'has_relations' => true,
        'relations' => ['Supplier' => 'suppliers']
    ],
    'Sale' => [
        'plural' => 'sales',
        'fields' => ['customer_id' => 'integer', 'total_amount' => 'decimal', 'sale_date' => 'string'],
        'has_relations' => true,
        'relations' => ['Customer' => 'customers']
    ]
];

foreach ($resources as $model => $meta) {
    $plural = $meta['plural'];
    $lower = strtolower($model);
    $fields = $meta['fields'];

    // GENERATE CONTROLLER
    $validationRules = [];
    foreach ($fields as $field => $type) {
        $rule = 'required';
        if ($type === 'string') $rule .= '|string|max:255';
        if ($type === 'text') $rule = 'nullable|string';
        if ($type === 'decimal') $rule .= '|numeric';
        if ($type === 'integer') $rule .= '|integer';
        $validationRules[] = "'$field' => '$rule'";
    }
    $validationRulesStr = implode(",\n            ", $validationRules);

    $relationPassCreate = "[]";
    // $relationPassEdit = "['$lower' => \\$$lower]";
    $relationPassEdit = "['$lower' => \$$lower]";
    
    if ($meta['has_relations']) {
        $relationPassCreate = "[";
        $relationPassEdit = "['$lower' => \$$lower, ";
        $arrItems = [];
        foreach ($meta['relations'] as $relModel => $relPlural) {
           $arrItems[] = "'$relPlural' => \\App\\Models\\$relModel::all()";
        }
        $relationPassCreate .= implode(', ', $arrItems) . "]";
        $relationPassEdit .= implode(', ', $arrItems) . "]";
    }
    
    $withRelations = "";
    if ($meta['has_relations']) {
        $withRelations = "->with([" . implode(', ', array_map(function($r) { return "'" . strtolower($r) . "'"; }, array_keys($meta['relations']))) . "])";
    }

    $controllerCode = <<<PHP
<?php

namespace App\Http\Controllers;

use App\Models\\$model;
use Illuminate\Http\Request;
use Inertia\Inertia;

class {$model}Controller extends Controller
{
    public function index()
    {
        \$items = $model::latest(){$withRelations}->paginate(10);
        return Inertia::render(ucfirst('$plural').'/Index', [
            '$plural' => \$items
        ]);
    }

    public function create()
    {
        return Inertia::render(ucfirst('$plural').'/Create', $relationPassCreate);
    }

    public function store(Request \$request)
    {
        \$data = \$request->validate([
            $validationRulesStr
        ]);

        $model::create(\$data);
        return redirect()->route('{$plural}.index')->with('message', '$model created successfully.');
    }

    public function edit($model \${$lower})
    {
        return Inertia::render(ucfirst('$plural').'/Edit', $relationPassEdit);
    }

    public function update(Request \$request, $model \${$lower})
    {
        \$data = \$request->validate([
            $validationRulesStr
        ]);

        \${$lower}->update(\$data);
        return redirect()->route('{$plural}.index')->with('message', '$model updated successfully.');
    }

    public function destroy($model \${$lower})
    {
        \${$lower}->delete();
        return redirect()->route('{$plural}.index')->with('message', '$model deleted successfully.');
    }
}
PHP;
    file_put_contents(__DIR__ . "/app/Http/Controllers/{$model}Controller.php", $controllerCode);

    // GENERATE VUE COMPONENTS
    $vueDir = __DIR__ . "/resources/js/Pages/" . ucfirst($plural);
    if (!is_dir($vueDir)) {
        mkdir($vueDir, 0777, true);
    }

    // LIST FIELDS (table columns)
    $tableHeads = "";
    $tableCells = "";
    foreach ($fields as $field => $type) {
        $label = ucfirst(str_replace('_id', '', $field));
        $tableHeads .= "<th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300\">$label</th>\n";
        
        if (str_ends_with($field, '_id')) {
            $rel = str_replace('_id', '', $field);
            $tableCells .= "<td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100\">{{ item.$rel?.name || item.$rel?.id }}</td>\n";
        } else {
            $tableCells .= "<td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100\">{{ item.$field }}</td>\n";
        }
    }

    $indexVue = <<<VUE
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    $plural: Object
});

const form = useForm({});

const destroy = (id) => {
    if (confirm('Are you sure you want to delete this $lower?')) {
        form.delete(route('{$plural}.destroy', id));
    }
};
</script>

<template>
    <Head title="$model List" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    $model List
                </h2>
                <Link
                    :href="route('{$plural}.create')"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition ease-in-out duration-150"
                >
                    Create $model
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        $tableHeads
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="item in {$plural}.data" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        $tableCells
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('{$plural}.edit', item.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Edit</Link>
                                            <button @click="destroy(item.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
VUE;
    file_put_contents("$vueDir/Index.vue", $indexVue);

    // GENERATE CREATE/EDIT
    $formFields = "";
    foreach ($fields as $field => $type) {
        $label = ucfirst(str_replace('_id', '', $field));
        
        if (str_ends_with($field, '_id')) {
            $rel = str_replace('_id', '', $field);
            $relPlural = $rel . 's';
            if ($rel === 'category') $relPlural = 'categories';
            $formFields .= <<<VUE
                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">$label</label>
                            <select v-model="form.$field" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700">
                                <option v-for="relItem in $relPlural" :key="relItem.id" :value="relItem.id">{{ relItem.name || relItem.id }}</option>
                            </select>
                            <div v-if="form.errors.$field" class="text-red-500 text-xs mt-1">{{ form.errors.$field }}</div>
                        </div>

VUE;
        } else if ($type === 'text') {
            $formFields .= <<<VUE
                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">$label</label>
                            <textarea v-model="form.$field" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700"></textarea>
                            <div v-if="form.errors.$field" class="text-red-500 text-xs mt-1">{{ form.errors.$field }}</div>
                        </div>

VUE;
        } else {
            $inputType = $type === 'integer' || $type === 'decimal' ? 'number' : 'text';
            $formFields .= <<<VUE
                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">$label</label>
                            <input type="$inputType" v-model="form.$field" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700">
                            <div v-if="form.errors.$field" class="text-red-500 text-xs mt-1">{{ form.errors.$field }}</div>
                        </div>

VUE;
        }
    }

    $formFieldsInit = "";
    foreach ($fields as $field => $type) {
        $formFieldsInit .= "$field: props.$lower ? props.$lower.$field : '',\n    ";
    }
    $propsItems = [];
    if ($meta['has_relations']) {
        foreach ($meta['relations'] as $relModel => $relPlural) {
           $propsItems[] = "$relPlural: Array";
        }
    }
    $propsDef = empty($propsItems) ? "" : implode(",\n    ", $propsItems);

    $createEditVue = <<<VUE
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    $lower: Object,
    $propsDef
});

const isEdit = !!props.$lower;

const form = useForm({
    $formFieldsInit
});

const submit = () => {
    if (isEdit) {
        form.put(route('{$plural}.update', props.{$lower}.id));
    } else {
        form.post(route('{$plural}.store'));
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit $model' : 'Create $model'" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ isEdit ? 'Edit $model' : 'Create $model' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit">
                            $formFields

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
VUE;
    file_put_contents("$vueDir/Create.vue", $createEditVue);
    file_put_contents("$vueDir/Edit.vue", $createEditVue);
    echo "Generated $model components\n";
}
