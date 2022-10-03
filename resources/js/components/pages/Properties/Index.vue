<template>
    <b-container fluid="xxl">
        <div class="row">
            <div class="row mt-3">
                <b-input-group>
                    <input :value="this.searchQuery" id="searchValue" class="form-control">
                    <b-input-group-append>
                        <b-button variant="secondary" type="primary" @click="this.searchProperties"><font-awesome-icon icon="fa-magnifying-glass" /></b-button>
                    </b-input-group-append>
                </b-input-group>
            </div>
        </div>

        <div class="row mt-3" v-if="propertiesLoaded">
            <div class="col-lg-5">
                <template>
                    <Map :locations="properties" @eventname="updateparent"></Map>
                </template>
            </div>
            <div class="col-lg-7">
                <div class="row">
                    <div v-for="property in properties" class="col-sm-3">
                        <b-card
                            :title="property['address']"
                            :sub-title="property['city']"
                            :img-src="property['image']"
                            onerror="this.src='/storage/icons/house.svg'"
                            img-top
                            tag="article"
                            class="mb-2"
                        >
                            <b-card-text>
                                ID: {{ property['id'] }}
                                Baths: {{ property['bath'] }}
                                Bed: {{ property['bed'] }}
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
        <div v-else class="row" style="margin-left: 200px; margin-right: 200px; margin-top: -150px"><img src="/storage/icons/loader.gif" alt="Loading..."></div>
    </b-container>
</template>
<script>
import Map from '../../Map.vue'
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
            zoom: 2,
            center: [0, 0],
            rotation: 0,
            searchQuery: '',
            propertiesLoaded: false
        }
    },
    components: {
        Map
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
                    this.properties = JSON.parse(JSON.stringify(data));
                    this.propertiesLoaded = true;

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
        },
        searchProperties: function () {
            this.propertiesLoaded = false;
            this.searchQuery = document.getElementById('searchValue').value;
            fetch("/api/properties/search", {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                method: "POST",
                body: JSON.stringify({
                    searchQuery: document.getElementById('searchValue').value
                })
            })
                .then(response => response.json())
                .then(data => {
                    this.properties = data;
                    this.propertiesLoaded = true;
                })
                .catch(error => { console.log(error); });
        },
        updateparent(coordinates) {
            console.log('testttt');
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
    height: 250px;
    width: 250px;
}

#app {
    font-family: Avenir, Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
    margin-top: 60px;
}

html, body, #map, #app {
    height: 100%;
    margin: 0;
    padding: 0;
}
</style>
