<template>
    <Head title="Pesquisadores" />
    <AuthenticatedLayout title="Pesquisadores">
        <div class="tw-py-l2">
            <div class="tw-max-w-7x1 tw-nx-auto sm:tw-px-6 lg:tw-px-8">
                <div class="tw-bg-white tw-overflow-hidden tw-shadow-sm sm:tw-rounded-lg">
                    <q-table
                        flat
                        bordered
                        :rows="researchers"
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
                                <div v-else-if="props.col.name == 'birth_date'">{{ getFormattedDate(props.value) }}</div>
                                <div v-else-if="props.col.name == 'institution'">{{ props.value.fantasy_name }}</div>
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
                        <div class="text-h6" v-if="typeModal == 1">Adicionar Pesquisador</div>
                        <div class="text-h6" v-if="typeModal == 2">Editar Pesquisador</div>
                    </q-card-section>

                    <q-card-section>
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                            <div>
                                <InputLabel value="Nome" required />
                                <q-input
                                    outlined
                                    clearable
                                    v-model="form.name"
                                    :error-message="form.errors.name"
                                    :error="form.errors.name"
                                />
                            </div>
                            <div>
                                <InputLabel value="Data de Nascimento" required />
                                <q-input
                                    outlined
                                    clearable
                                    mask="##/##/####"
                                    v-model="form.birth_date"
                                    :error-message="form.errors.birth_date"
                                    :error="form.errors.birth_date"
                                >
                                    <template v-slot:append>
                                        <q-icon name="event" class="cursor-pointer">
                                        <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                            <q-date minimal mask="DD/MM/YYYY" v-model="form.birth_date">
                                            <div class="row items-center justify-end">
                                                <q-btn v-close-popup label="Fechar" color="primary" flat />
                                            </div>
                                            </q-date>
                                        </q-popup-proxy>
                                        </q-icon>
                                    </template>
                                </q-input>
                            </div>
                            <div>
                                <InputLabel value="Sexo" required />
                                <q-select
                                    outlined
                                    clearable
                                    v-model="form.sex"
                                    :options="sexOptions"
                                    :error-message="form.errors.sex"
                                    :error="form.errors.sex"
                                />
                            </div>
                            <div>
                                <InputLabel value="CPF" required />
                                <q-input
                                    outlined
                                    clearable
                                    mask="###.###.###-##"
                                    v-model="form.cpf"
                                    :error-message="form.errors.cpf"
                                    :error="form.errors.cpf"
                                />
                            </div>
                            <div>
                                <InputLabel value="Universidade" required />
                                <q-input
                                    outlined
                                    clearable
                                    v-model="form.university"
                                    :error-message="form.errors.university"
                                    :error="form.errors.university"
                                />
                            </div>
                            <div>
                                <InputLabel value="Instituição" required />
                                <q-select
                                    outlined
                                    clearable
                                    v-model="form.institution"
                                    option-value="id"
                                    option-label="fantasy_name"
                                    :options="institutions"
                                    :error-message="form.errors.institution"
                                    :error="form.errors.institution"
                                />
                            </div>
                        </div>
                        <div class="tw-mt-5 tw-flex tw-justify-center q-gutter-sm">
                            <q-btn unelevated rounded v-close-popup color="primary" icon="add" v-if="typeModal == 1" @click="storeResearcher()" label="Cadastrar" />
                            <q-btn unelevated rounded v-close-popup color="primary" icon="save" v-if="typeModal == 2" @click="updateResearcher()" label="Salvar" />
                            <q-btn unelevated rounded v-close-popup color="red" icon="cancel" label="Cancelar" />
                        </div>
                    </q-card-section>
                </q-card>
            </q-dialog>

            <q-dialog v-model="openDetailModal">
                <q-card style="width: 700px; max-width: 80vw;">
                    <q-card-section>
                        <div class="text-h6">Detalhes do Pesquisador</div>
                    </q-card-section>

                    <q-card-section>
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                            <div>
                                <InputLabel class="tw-h6" value="Nome" />
                                <span class="tw-text-lg">{{ form.name }}</span>
                            </div>
                            <div>
                                <InputLabel value="Data de Nascimento" />
                                <span class="tw-text-lg">{{ form.birth_date }}</span>
                            </div>
                            <div>
                                <InputLabel value="Sexo" />
                                <span class="tw-text-lg">{{ form.sex }}</span>
                            </div>
                            <div>
                                <InputLabel value="CPF" />
                                <span class="tw-text-lg">{{ form.cpf }}</span>
                            </div>
                            <div>
                                <InputLabel value="Universidade" />
                                <span class="tw-text-lg">{{ form.university }}</span>
                            </div>
                            <div>
                                <InputLabel value="Instituição" />
                                <span class="tw-text-lg">{{ form.institution.fantasy_name }}</span>
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
                        <div class="text-h6">Excluir Pesquisador?</div>
                    </q-card-section>
                    <div class="tw-flex tw-justify-center">
                        <q-btn flat label="Não" color="primary" v-close-popup />
                        <q-btn flat label="Sim" color="red" @click="deleteResearcher()" v-close-popup />
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
    researchers: {
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
    { name: 'name', align: 'center', label: 'Nome', field: 'name', sortable: true},
    { name: 'birth_date', align: 'center', label: 'Data de Nascimento', field: 'birth_date', sortable: true},
    { name: 'sex', align: 'center', label: 'Sexo', field: 'sex', sortable: true},
    { name: 'cpf', align: 'center', label: 'CPF', field: 'cpf', sortable: true},
    { name: 'university', align: 'center', label: 'Universidade', field: 'university', sortable: true},
    { name: 'institution', align: 'center', label: 'Instituição', field: 'institution', sortable: true},
    { name: 'actions', align: 'center', label: 'Ações', field: 'actions', sortable: false},
];

const paginationLabels = {
    rowsPerPage: 'Linhas por página',
    pagination: (start, end, total) => `${start}-${end} de ${total}`,
    prev: 'Anterior',
    next: 'Próximo',
    all: 'Todos',
};

const sexOptions = [
    'Masculino',
    'Feminino',
    'Outro',
];

const form = useForm({
    id: '',
    name: '',
    birth_date: '',
    sex: '',
    cpf: '',
    university: '',
    institution: null,
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
            form.name = data.name;
            form.birth_date = getFormattedDate(data.birth_date);
            form.sex = data.sex;
            form.cpf = data.cpf;
            form.university = data.university;
            form.institution = data.institution;
        }
    }
}

function openDetailModalAction(item) {
    openDetailModal.value = true;
    form.id = item.id;
    form.name = item.name;
    form.birth_date = getFormattedDate(item.birth_date);
    form.sex = item.sex;
    form.cpf = item.cpf;
    form.university = item.university;
    form.institution = item.institution;
    form.works = item.works;
}

function openDeleteModalAction(item) {
    openDeleteModal.value = true;
    form.id = item.id;
}

function storeResearcher() {
    form.birth_date = setFormattedDate(form.birth_date)
    form.post(route('researcher.store'), {
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

function updateResearcher() {
    form.birth_date = setFormattedDate(form.birth_date)
    form.put(route('researcher.update', form.id), {
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

function deleteResearcher() {
    form.delete(route('researcher.delete', form.id), {
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

function setFormattedDate(date) {
    const parts = date.split('/');
    const day = parts[0];
    const mounth = parts[1];
    const year = parts[2];
    return `${year}-${mounth}-${day}`;
}

function getFormattedDate(date) {
    const newDate = new Date(date);
    const day = newDate.getDate().toString().padStart(2, '0');
    const month = (newDate.getMonth() + 1).toString().padStart(2, '0');
    const year = newDate.getFullYear();
    return `${day}/${month}/${year}`;
}

</script>
