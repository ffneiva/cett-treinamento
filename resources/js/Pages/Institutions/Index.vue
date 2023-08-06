<template>
    <Head title="Instituições" />
    <AuthenticatedLayout title="Instituições">
        <div class="tw-py-l2">
            <div class="tw-max-w-7x1 tw-nx-auto sm:tw-px-6 lg:tw-px-8">
                <div class="tw-bg-white tw-overflow-hidden tw-shadow-sm sm:tw-rounded-lg">
                    <q-table
                        flat
                        bordered
                        :rows="institutions"
                        :columns="columns"
                        :filter="filter"
                        :pagination-labels="paginationLabels"
                        row-key="name"
                        no-data-label="Sem dados cadastrados"
                        no-results-label="Nenhum dado corresponde à pesquisa"
                    >
                        <template v-slot:top-left>
                            <q-btn color="primary" icon="add" @click="openModalAction(1)" label="Adicionar" />
                        </template>

                        <template v-slot:top-right>
                            <q-input borderless dense v-model="filter" outlined placeholder="Pesquisar">
                                <template v-slot:append>
                                    <q-icon name="search" />
                                </template>
                            </q-input>
                        </template>

                        <template v-slot:body-cell="props">
                            <q-td :props="props">
                                <div v-if="props.col.name =='actions'">
                                    <div class="tw-inline-flex tw-items-center tw-rounded-nd tw-shadow-sn">
                                        <q-btn flat round size="sm" color="primary" icon="edit" @click="openModalAction(2, props.row)" />
                                        <q-btn flat round size="sm" color="purple" icon="visibility" @click="openDetailModalAction(props.row)" />
                                        <q-btn flat round size="sm" color="red" icon="delete" @click="openDeleteModalAction(props.row)" />
                                    </div>
                                </div>
                                <div v-else>{{ props.value }}</div>
                            </q-td>
                        </template>

                        <template v-slot:no-data="{ icon, message, filter }">
                            <div class="tw-w-full tw-flex tw-justify-center tw-items-center">
                                <span>{{ message }}</span>
                                <q-icon size="2em" name="sentiment_dissatiefied" />
                            </div>
                        </template>
                    </q-table>
                </div>
            </div>

            <q-dialog v-model="openModal">
                <q-card class="min-width: 40vw; max-width: 500px; border-radius: 20px; padding: 40px;">
                    <q-card-section class="">
                        <div class="text-h6" v-if="typeModal == 1">Adicionar Instituição</div>
                        <div class="text-h6" v-if="typeModal == 2">Editar Instituição</div>
                    </q-card-section>

                    <q-card-section>
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                            <div>
                                <InputLabel value="Razão Social" required />
                                <q-input
                                    outlined
                                    clearable
                                    v-model="form.corporate_name"
                                    :error-message="form.errors.corporate_name"
                                    :error="form.errors.corporate_name"
                                />
                            </div>
                            <div>
                                <InputLabel value="Nome Fantasia" required />
                                <q-input
                                    outlined
                                    clearable
                                    v-model="form.fantasy_name"
                                    :error-message="form.errors.fantasy_name"
                                    :error="form.errors.fantasy_name"
                                />
                            </div>
                            <div>
                                <InputLabel value="CNPJ" required />
                                <q-input
                                    outlined
                                    clearable
                                    mask="##.###.###/####-##"
                                    v-model="form.cnpj"
                                    :error-message="form.errors.cnpj"
                                    :error="form.errors.cnpj"
                                />
                            </div>
                            <div>
                                <InputLabel value="Tipo" required />
                                <q-input
                                    outlined
                                    clearable
                                    v-model="form.type"
                                    :error-message="form.errors.type"
                                    :error="form.errors.type"
                                />
                            </div>
                        </div>
                        <div class="tw-mt-5 tw-flex tw-justify-center q-gutter-sm">
                            <q-btn unelevated rounded v-close-popup color="primary" icon="add" v-if="typeModal == 1" @click="storeInstitution()" label="Cadastrar" />
                            <q-btn unelevated rounded v-close-popup color="primary" icon="save" v-if="typeModal == 2" @click="updateInstitution()" label="Salvar" />
                            <q-btn unelevated rounded v-close-popup color="red" icon="cancel" label="Cancelar" />
                        </div>
                    </q-card-section>
                </q-card>
            </q-dialog>

            <q-dialog v-model="openDetailModal">
                <q-card style="width: 700px; max-width: 80vw;">
                    <q-card-section>
                        <div class="text-h6">Detalhes da Instituição</div>
                    </q-card-section>

                    <q-card-section>
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                            <div>
                                <InputLabel class="tw-h6" value="Razão Social" />
                                <span class="tw-text-lg">{{ form.corporate_name }}</span>
                            </div>
                            <div>
                                <InputLabel value="Nome Fantasia" />
                                <span class="tw-text-lg">{{ form.fantasy_name }}</span>
                            </div>
                            <div>
                                <InputLabel value="CNPJ" />
                                <span class="tw-text-lg">{{ form.cnpj }}</span>
                            </div>
                            <div>
                                <InputLabel value="Tipo" />
                                <span class="tw-text-lg">{{ form.type }}</span>
                            </div>
                            <div class="tw-nt-5" v-if="form.researchers?.length > 0">
                                <span class="tw-text-x1">Pesquisadores</span>
                                <q-list bordered separator class="tw-nt-3">
                                    <q-item v-for="(researcher, index) in form.researchers" :key="index">
                                        <q-item-section>{{ index + 1 }} - {{ researcher.name }}</q-item-section>
                                    </q-item>
                                </q-list>
                            </div>
                            <div class="tw-nt-5" v-if="form.works?.length > 0">
                                <span class="tw-text-x1">Obras</span>
                                <q-list bordered separator class="tw-nt-3">
                                    <q-item v-for="(work, index) in form.works" :key="index">
                                        <q-item-section>{{ index + 1 }} - {{ work.title }}</q-item-section>
                                    </q-item>
                                </q-list>
                            </div>
                        </div>
                        <div class="tw-mt-4 tw-flex tw-justify-center">
                            <q-btn color="primary" icon="close" label="Fechar" v-close-popup />
                        </div>
                    </q-card-section>
                </q-card>
            </q-dialog>

            <q-dialog v-model="openDeleteModal">
                <q-card>
                    <q-card-section class="row items-center">
                        <div class="text-h6">Excluir Instituição?</div>
                    </q-card-section>
                    <div class="tw-flex tw-justify-center">
                        <q-btn flat label="Não" color="primary" v-close-popup />
                        <q-btn flat label="Sim" color="red" @click="deleteInstitution()" v-close-popup />
                    </div>
                </q-card>
            </q-dialog>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Icon } from '@iconify/vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

defineProps({
    institutions: {
        type: Array,
        default: [],
    },
});

const filter = ref('');
const openModal = ref(false);
const openDetailModal = ref(false);
const openDeleteModal = ref(false);
const typeModal = ref(1);

const columns = [
    { name: 'fantasy_name', align: 'center', label: 'Nome Fantasia', field: 'fantasy_name', sortable: true},
    { name: 'cnpj', align: 'center', label: 'CNPJ', field: 'cnpj', sortable: true},
    { name: 'type', align: 'center', label: 'Tipo', field: 'type', sortable: true},
    { name: 'actions', align: 'center', label: 'Ações', field: 'actions', sortable: false},
];

const paginationLabels = {
    rowsPerPage: 'Linhas por página',
    pagination: (start, end, total) => `${start}-${end} de ${total}`,
    prev: 'Anterior',
    next: 'Próximo',
    all: 'Todos',
};

const form = useForm({
    id: '',
    fantasy_name: '',
    corporate_name: '',
    cnpj: '',
    type: '',
    researchers: [],
    works: [],
});

function openModalAction(type, data = []) {
    openModal.value = true;
    if (type == 1) {
        typeModal.value = 1;
        form.reset();
    } else {
        if(data) {
            typeModal.value = 2;
            form.id = data.id;
            form.fantasy_name = data.fantasy_name;
            form.corporate_name = data.corporate_name;
            form.cnpj = data.cnpj;
            form.type = data.type;
        }
    }
}

function openDetailModalAction(item) {
    openDetailModal.value = true;
    form.id = item.id;
    form.fantasy_name = item.fantasy_name;
    form.corporate_name = item.corporate_name;
    form.cnpj = item.cnpj;
    form.type = item.type;
    form.researchers = item.researchers;
    form.works = item.works;
}

function openDeleteModalAction(item) {
    openDeleteModal.value = true;
    form.id = item.id;
}

function storeInstitution() {
    form.post(route('institution.store'), {
        preserveScroll: true,
        onSuccess: () => {
            openModal.value = false;
            form.reset();
        },
        onError: (error) => {
            console.log(error);
        },
    });
}

function updateInstitution() {
    form.put(route('institution.update', form.id), {
        preserveScroll: true,
        onSuccess: () => {
            openModal.value = false;
            form.reset();
        },
        onError: (error) => {
            console.log(error);
        },
    });
}

function deleteInstitution() {
    form.delete(route('institution.delete', form.id), {
        preserveScroll: true,
        onSuccess: () => {
            openDeleteModal.value = false;
            form.reset();
        },
        onError: (error) => {
            console.log(error);
        },
    });
}

</script>
