<template>
    <Head title="Obras" />
    <AuthenticatedLayout title="Obras">
        <div class="tw-py-l2">
            <div class="tw-max-w-7x1 tw-nx-auto sm:tw-px-6 lg:tw-px-8">
                <div class="tw-bg-white tw-overflow-hidden tw-shadow-sm sm:tw-rounded-lg">
                    <q-table
                        flat
                        bordered
                        :rows="works"
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
                                <div v-else-if="props.col.name == 'publication_date'">{{ getFormattedDate(props.value) }}</div>
                                <div v-else-if="props.col.name == 'researchers'">{{ props.value.length }}</div>
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
                        <div class="text-h6" v-if="typeModal == 1">Adicionar Obra</div>
                        <div class="text-h6" v-if="typeModal == 2">Editar Obra</div>
                    </q-card-section>

                    <q-card-section>
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                            <div>
                                <InputLabel value="Título" required />
                                <q-input
                                    outlined
                                    clearable
                                    v-model="form.title"
                                    :error-message="form.errors.title"
                                    :error="form.errors.title"
                                />
                            </div>
                            <div>
                                <InputLabel value="Data de Publicação" required />
                                <q-input
                                    outlined
                                    clearable
                                    mask="##/##/####"
                                    v-model="form.publication_date"
                                    :error-message="form.errors.publication_date"
                                    :error="form.errors.publication_date"
                                >
                                    <template v-slot:append>
                                        <q-icon name="event" class="cursor-pointer">
                                        <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                                            <q-date minimal mask="DD/MM/YYYY" v-model="form.publication_date">
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
                            <div>
                                <InputLabel value="Assunto" required />
                                <q-input
                                    outlined
                                    clearable
                                    v-model="form.subject"
                                    :error-message="form.errors.subject"
                                    :error="form.errors.subject"
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
                            <div>
                                <InputLabel value="Nota" required />
                                <q-rating
                                    size="1.5em"
                                    color="primary"
                                    v-model="form.grade"
                                    :max="10"
                                    :error-message="form.errors.grade"
                                    :error="form.errors.grade"
                                />
                            </div>
                            <div>
                                <InputLabel value="Língua" required />
                                <q-input
                                    outlined
                                    clearable
                                    v-model="form.language"
                                    :error-message="form.errors.language"
                                    :error="form.errors.language"
                                />
                            </div>
                            <div>
                                <InputLabel value="Licença" required />
                                <q-input
                                    outlined
                                    clearable
                                    v-model="form.license"
                                    :error-message="form.errors.license"
                                    :error="form.errors.license"
                                />
                            </div>
                        </div>
                        <div class="tw-mt-4 tw-grid">
                            <div>
                                <InputLabel value="Pesquisadores" required />
                                <q-select
                                    outlined
                                    clearable
                                    multiple
                                    v-model="form.researchers"
                                    option-value="id"
                                    option-label="name"
                                    :options="researchers"
                                    :error-message="form.errors.researchers"
                                    :error="form.errors.researchers"
                                />
                            </div>
                        </div>
                        <div class="tw-mt-4 tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                            <div>
                                <InputLabel v-if="typeModal == 1" value="Thumb" required />
                                <InputLabel v-if="typeModal == 2" value="Thumb" />
                                <q-file
                                    label="Thumb"
                                    outlined
                                    clearable
                                    v-model="form.thumb"
                                    :error-message="form.errors.thumb"
                                    :error="form.errors.thumb"
                                >
                                    <template v-slot:prepend>
                                        <q-icon name="cloud_upload" @click.stop.prevent />
                                    </template>
                                </q-file>
                            </div>
                            <div>
                                <InputLabel v-if="typeModal == 1" value="Arquivo" required />
                                <InputLabel v-if="typeModal == 2" value="Arquivo" />
                                <q-file
                                    label="Arquivo"
                                    outlined
                                    clearable
                                    type="file"
                                    v-model="form.file"
                                    :error-message="form.errors.file"
                                    :error="form.errors.file"
                                >
                                    <template v-slot:prepend>
                                        <q-icon name="cloud_upload" label="Thumb" @click.stop.prevent />
                                    </template>
                                </q-file>
                            </div>

                            <div class="tw-mt-5 tw-flex tw-justify-center q-gutter-sm">
                                <q-btn unelevated rounded v-close-popup color="primary" icon="add" v-if="typeModal == 1" @click="storeWork()" label="Cadastrar" />
                                <q-btn unelevated rounded v-close-popup color="primary" icon="save" v-if="typeModal == 2" @click="updateWork()" label="Salvar" />
                            </div>
                            <div class="tw-mt-5 tw-flex tw-justify-center q-gutter-sm">
                                <q-btn unelevated rounded v-close-popup color="red" icon="cancel" label="Cancelar" />
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </q-dialog>

            <q-dialog v-model="openDetailModal">
                <q-card style="width: 700px; max-width: 80vw;">
                    <q-card-section>
                        <div class="text-h6">Detalhes da Obra</div>
                    </q-card-section>

                    <q-card-section>
                        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                            <div>
                                <InputLabel class="tw-h6" value="Título" />
                                <span class="tw-text-lg">{{ form.title }}</span>
                            </div>
                            <div>
                                <InputLabel value="Data da Publicação" />
                                <span class="tw-text-lg">{{ form.publication_date }}</span>
                            </div>
                            <div>
                                <InputLabel value="Instituição" />
                                <span class="tw-text-lg">{{ form.institution.fantasy_name }}</span>
                            </div>
                            <div>
                                <InputLabel value="Assunto" />
                                <span class="tw-text-lg">{{ form.subject }}</span>
                            </div>
                            <div>
                                <InputLabel value="Tipo" />
                                <span class="tw-text-lg">{{ form.type }}</span>
                            </div>
                            <div>
                                <InputLabel value="Nota" />
                                <span class="tw-text-lg">{{ form.grade }}</span>
                            </div>
                            <div>
                                <InputLabel value="Língua" />
                                <span class="tw-text-lg">{{ form.language }}</span>
                            </div>
                            <div>
                                <InputLabel value="Licença" />
                                <span class="tw-text-lg">{{ form.license }}</span>
                            </div>
                        </div>
                        <div class="tw-mt-4 tw-grid tw-gap-4">
                            <div>
                                <InputLabel value="Pesquisadores" />
                                <q-list bordered separator class="tw-nt-3">
                                    <q-item v-for="(researcher, index) in form.researchers" :key="index">
                                        <q-item-section>{{ index + 1 }} - {{ researcher.name }}</q-item-section>
                                    </q-item>
                                </q-list>
                            </div>
                        </div>
                        <div class="tw-mt-4 tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                            <div v-if="form.thumb !== null">
                                <InputLabel value="Thumbnail" />
                                <a :download="`Thumbnail - ${form.title}`" :href="`storage/${form.thumb}`">
                                    <q-btn flat color="primary" icon="download" label="Baixar arquivo" />
                                </a>
                            </div>
                            <div v-if="form.file !== null">
                                <InputLabel value="Arquivo" />
                                <a :download="`Arquivo - ${form.title}`" :href="`storage/${form.file}`">
                                    <q-btn flat color="primary" icon="download" label="Baixar arquivo" @click="downloadFile(form.file)" />
                                </a>
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
                        <div class="text-h6">Excluir Obra?</div>
                    </q-card-section>
                    <div class="tw-flex tw-justify-center">
                        <q-btn flat label="Não" color="primary" v-close-popup />
                        <q-btn flat label="Sim" color="red" @click="deleteWork()" v-close-popup />
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
    works: {
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
    { name: 'title', align: 'center', label: 'Título', field: 'title', sortable: true},
    { name: 'publication_date', align: 'center', label: 'Data da Publicação', field: 'publication_date', sortable: true},
    { name: 'institution', align: 'center', label: 'Instituição', field: 'institution', sortable: true},
    { name: 'researchers', align: 'center', label: 'Pesquisadores', field: 'researchers', sortable: true},
    { name: 'subject', align: 'center', label: 'Assunto', field: 'subject', sortable: true},
    { name: 'type', align: 'center', label: 'Tipo', field: 'type', sortable: true},
    { name: 'grade', align: 'center', label: 'Nota', field: 'grade', sortable: true},
    { name: 'language', align: 'center', label: 'Língua', field: 'language', sortable: true},
    { name: 'license', align: 'center', label: 'Licença', field: 'license', sortable: true},
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
    title: '',
    publication_date: '',
    institution: null,
    subject: '',
    type: '',
    grade: 0,
    language: '',
    license: '',
    researchers: [],
    thumb: null,
    file: null,
});

function openModalAction(type, data = []) {
    openModal.value = true;
    form.reset();
    if (type == 1) {
        typeModal.value = 1;
    } else {
        if(data) {
            typeModal.value = 2;
            form.id = data.id;
            form.title = data.title;
            form.publication_date = getFormattedDate(data.publication_date);
            form.institution = data.institution;
            form.subject = data.subject;
            form.type = data.type;
            form.grade = data.grade;
            form.language = data.language;
            form.license = data.license;
            form.researchers = data.researchers;
        }
    }
}

function openDetailModalAction(item) {
    openDetailModal.value = true;
    form.id = item.id;
    form.title = item.title;
    form.publication_date = getFormattedDate(item.publication_date);
    form.institution = item.institution;
    form.subject = item.subject;
    form.type = item.type;
    form.grade = item.grade;
    form.language = item.language;
    form.license = item.license;
    form.researchers = item.researchers;
    form.thumb = item.thumb;
    form.file = item.file;
}

function openDeleteModalAction(item) {
    openDeleteModal.value = true;
    form.id = item.id;
}

function storeWork() {
    form.publication_date = setFormattedDate(form.publication_date)
    form.post(route('work.store'), {
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

function updateWork() {
    form.publication_date = setFormattedDate(form.publication_date)
    form.post(route('work.update', form.id), {
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

function deleteWork() {
    form.delete(route('work.delete', form.id), {
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
