<template>
    <main role="main">
        <div class="album py-5 bg-light" style="background: #e9e9e9 !important; border-radius: 5px; margin: 15px;">
            <div class="container">
                <div class="row ml-5 mr-5">
                    <div  class="col-sm-6" style="border-bottom: 1px solid lightgray; margin-bottom: 15px; padding-bottom: 10px;">
                        <h1 style="margin-bottom: 0 !important;">{{ this.heading }}</h1>
                    </div>
                    <div  class="col-sm-2" style="border-bottom: 1px solid lightgray; margin-bottom: 15px; padding-bottom: 10px;">
                        <button class="btn btn-secondary" v-on:click="onMap($event)" >
                            Show on Map <font-awesome-icon icon="fa-solid fa-map-location" />
                        </button>
                    </div>
                    <div  class="col-sm-2" style="border-bottom: 1px solid lightgray; margin-bottom: 15px; padding-bottom: 10px;">
                        <button class="btn btn-secondary" v-on:click="asGrid($event)" >
                            Show as a Grid <font-awesome-icon icon="fa-solid fa-grid" />
                        </button>
                    </div>
                    <div  class="col-sm-2" style="border-bottom: 1px solid lightgray; margin-bottom: 15px; padding-bottom: 10px;">
                        <button class="btn btn-secondary" v-if="this.type === 'search-landing'">
                            <router-link :to="{ name: 'home' }">
                                Back <font-awesome-icon icon="fa-solid fa-circle-arrow-left" />
                            </router-link>
                        </button>
                        <button class="btn btn-secondary" v-if="this.type === 'search-counties'">
                            <router-link :to="{ name: 'by-counties' }">
                                Back <font-awesome-icon icon="fa-solid fa-circle-arrow-left" />
                            </router-link>
                        </button>
                        <button class="btn btn-secondary" v-if="this.type === 'search-amenities'">
                            <router-link :to="{ name: 'by-amenities' }">
                                Back <font-awesome-icon icon="fa-solid fa-circle-arrow-left" />
                            </router-link>
                        </button>
                    </div>
                    <div  class="col-sm-3" v-for="property in JSON.parse(this.properties)" v-if="propertyGrid && searchResults">
                        <b-card
                            :title="property['address']"
                            :sub-title="property['city']"
                            :img-src="property['image']"
                            onerror="this.src='/storage/icons/house.svg'"
                            img-top
                            tag="article"
                            class="mb-2 shadow"
                        >
                            <b-card-text>
                                ID: {{ property['id'] }}
                                Baths: {{ property['bath'] }}
                                Bed: {{ property['bed'] }}
                            </b-card-text>
                            <b-button href="#" variant="secondary">
                                <router-link :to="{ path: '/properties/detail/'+ property['id'] }" v-on:click="details($event)">
                                    View More
                                </router-link>
                            </b-button>
                        </b-card>
                    </div>
                    <div v-if="!propertyGrid && searchResults">
                        <div class="col-lg-12" style="height: 100%">
                            <Map :locations="JSON.parse(this.properties)" :draw="false"></Map>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
import Map from '../../Map.vue'
export default {
    props: {
        properties: '',
        heading: '',
        type: ''
    },
    data: () => ({
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        propertyGrid: true,
        searchResults: true
    }),
    components: {
        Map
    },
    methods: {
        asGrid: function(event) {
            this.propertyGrid = true;
        },
        onMap: function(event) {
            this.propertyGrid = false;
        },
        details: function (event) {
            this.searchResults = false;
        }
    },
    name: 'PropertiesSearch',
    mounted() {
    }
}
</script>

<style scoped>
button {
    height: 90%;
    width: 100%;
}
 #map{
    height: 500px;
    margin: 0;
    padding: 0;
}
 a {
     color: white;
     text-decoration: none;
 }
.container {
    max-width: 3050px;
}
.card-img-top {
    height: 309px;
    width: 413px;
}
</style>
