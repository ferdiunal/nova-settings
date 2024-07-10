<template>
    <div>

        <Head :title="title === 'Nova Settings' ? title : `${title} • Nova Settings`" />
        <Heading class="mb-6">
            {{ title }}
        </Heading>

        <form autocomplete="off" @submit.prevent.stop="update" class="nova-settings-form">
            <div class="panel-group">
                <Panel v-for="({ fields, title, description }, index) in current_resources" :key="index" :title="title"
                    :description="description" :fields="fields" :errors="errors" />
            </div>
            <div
                class="flex mt-4 flex-col md:flex-row md:items-center justify-center md:justify-end space-y-2 md:space-y-0 md:space-x-3">
                <button type="submit" dusk="update-button" label="Save Settings" :disabled="loading" :loading="loading">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import { Errors } from 'laravel-nova';
import Panel from './Panel.vue';

export default {
    components: {
        Panel
    },
    props: {
        group: String
    },
    computed: {
        title() {
            return this.resources.find(resource => resource.group === this.group)?.title || 'Nova Settings';
        },
        current_resources() {
            return this.resources.filter(resource => resource.group === this.group)
        }
    },
    data() {
        return {
            loading: false,
            resources: [],
            errors: new Errors(),
        };
    },
    async mounted() {
        await this.getFields();
    },
    unmounted() {
        this.resources = [];
        this.activeTab = null;
        this.loading = false;
        this.errors.clear();
    },
    methods: {
        async getFields() {
            try {
                this.loading = true;
                const response = await Nova.request().get(`/nova-vendor/nova-settings/${this.group}`);
                this.resources = response.data.resources;
                this.loading = false;
            } catch (error) {
                this.loading = false;
                Errors.record(error);
            }
        },
        async update() {
            try {
                this.errors.clear();
                const formData = new FormData();
                this.current_resource.forEach(resource => resource.fields.forEach(field => field.fill(formData)));
                formData.append('_method', 'POST');

                await Nova.request().post(`/nova-vendor/nova-settings/${this.group}`, formData);
                this.errors.clear();
                await this.getFields();
                Nova.success('Settings have been saved!');
            } catch (error) {
                if (error.response.status === 422 && error.response.data.errors) {
                    this.errors = new Errors(error.response.data.errors || {});
                }
            }
        }
    },
}
</script>

<style>
/* Scoped Styles */
</style>
