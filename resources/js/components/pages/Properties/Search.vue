<template>
    <div class="row mt-5 ml-5 mr-5">
        <div  class="col-sm-6">
            <b-card
                img-top
                tag="article"
                class="mb-2"
            >
                <h1 class="text-center">{{ this.heading }}</h1>
            </b-card>
        </div>
        <div  class="col-sm-3">
            <button class="btn btn-secondary" v-on:click="onMap($event)" >
                <h1>SHOW ON MAP</h1>
            </button>
        </div>
        <div  class="col-sm-3">
            <button class="btn btn-secondary" v-on:click="asGrid($event)" >
                <h1>SHOW AS A GRID</h1>
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
                class="mb-2"
            >
                <b-card-text>
                    ID: {{ property['id'] }}
                    Baths: {{ property['bath'] }}
                    Bed: {{ property['bed'] }}
                </b-card-text>
                <b-button href="#" variant="primary">
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
</template>

<script>
import Map from '../../Map.vue'
export default {
    props: {
        properties: '',
        heading: ''
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
 }
</style>
