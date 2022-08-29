<template>
    <b-container fluid="xxl">
        <div class="row">
            <div class="row mt-3">
                <b-dropdown id="dropdown-1" text="Dropdown Button" class="m-md-2">
                    <b-dropdown-item>First Action</b-dropdown-item>
                    <b-dropdown-item>Second Action</b-dropdown-item>
                    <b-dropdown-item>Third Action</b-dropdown-item>
                    <b-dropdown-divider></b-dropdown-divider>
                    <b-dropdown-item active>Active action</b-dropdown-item>
                    <b-dropdown-item disabled>Disabled action</b-dropdown-item>
                </b-dropdown>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-lg-5">
                <GmapMap
                    :center="{lat: 36.77, lng: -119.41}"
                    :zoom="10"
                    map-type-id="terrain"
                    style="width: 100%; height: 100%"
                >
                    <GmapMarker
                        :key="index"
                        v-for="(m, index) in properties"
                        :position="{lat: m['location']['lat'], lng: m['location']['lon']}"
                    />
                </GmapMap>
            </div>
            <div class="col-lg-7">
                <div v-for="i in perPage" class="row">
                    <div v-for="j in (propertiesPerPage / perPage )" class="col-sm-3">
                        <b-card v-if=""
                            :title="properties[(i-1)* (propertiesPerPage / perPage ) + (j-1)]['address']"
                            :sub-title="properties[(i-1)* (propertiesPerPage / perPage ) + (j-1)]['city']"
                            :img-src="properties[(i-1)* (propertiesPerPage / perPage ) + (j-1)]['image']"
                                onerror="this.src='/storage/icons/house.svg'"
                            img-top
                            tag="article"
                            class="mb-2"
                        >
                            <b-card-text>
                                ID: {{ properties[(i-1)* (propertiesPerPage / perPage ) + (j-1)]['id'] }}
                                Baths: {{ properties[(i-1)* (propertiesPerPage / perPage ) + (j-1)]['bath'] }}
                                Bed: {{ properties[(i-1)* (propertiesPerPage / perPage ) + (j-1)]['bed'] }}
                            </b-card-text>
                            <b-button href="#" variant="primary">Go somewhere</b-button>
                        </b-card>
                    </div>
                </div>
                <div class="row">
                    <b-pagination
                        @input="handlePageClick"
                        v-model="currentPage"
                        :total-rows="rows"
                        :per-page="perPage"
                        first-text="First"
                        prev-text="Prev"
                        next-text="Next"
                        last-text="Last"
                    ></b-pagination>
                </div>
            </div>
        </div>

    </b-container>
</template>
<script>
export default {
    name: "PropertiesIndex",
    data() {
        return {
            properties: [],
            currentPage: 1,
            rows: 3869,
            perPage: 3,
            propertiesPerPage: 12,
            filter: 'none',
        }
    },
    computed: {
    },
    created() {
        this.fetchProperties();

    },
    mounted() {
    },
    methods: {
        fetchProperties: function () {
            fetch("/api/properties", {
                headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                },
                method: "POST",
                body: JSON.stringify({
                        size: this.propertiesPerPage,
                        filter: this.filter,
                        currentPage: this.currentPage
                })
            })
                .then(response => response.json())
                .then(data => {
                    this.properties = data;
                })
                .catch(error => { console.log(error); });
        },
        handlePageClick: function (pageNumber) {
            fetch("/api/properties", {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                method: "POST",
                body: JSON.stringify({
                    size: this.propertiesPerPage,
                    filter: this.filter,
                    currentPage: pageNumber
                })
            })
                .then(response => response.json())
                .then(data => {
                    this.properties = data;
                })
                .catch(error => { console.log(error); });
        }
    }
}
</script>

<style scoped>
.card-title {
    white-space: nowrap;
    text-overflow: "...";
    overflow: hidden;
}
.card-img-top {
    background-image: url("/storage/icons/house.svg");
}
</style>
