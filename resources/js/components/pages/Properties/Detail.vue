<template>
    <div class="row mt-3" v-if="propertiesLoaded">
        <div class="col-lg-5">
            <Map :locations="locations" :draw="false"></Map>
        </div>
        <div class="col-lg-7">
            <b-card
                :img-src="property['image']"
                onerror="this.src='/storage/icons/house.svg'"
                img-top
                tag="article"
                class="mb-2"
            >
                <b-card-text>
                    <div>
                        <h3>{{ property['address'] }}, {{ property['city'] }}</h3>
                        <h5>{{ property['description'] }}</h5>
                        Baths: {{ property['bath'] }}
                        Bed: {{ property['bed'] }}
                    </div>
                    <ul>
                        <h6 style="margin-left: -30px">Schools in this area:</h6>
                        <li v-for="school in schools">
                            {{ school["NAME"] }}, {{ school["CITY"] }}, {{ school["STATE"] }}
                        </li>
                    </ul>
                </b-card-text>
            </b-card>
        </div>
    </div>
</template>

<script>
import Map from '../../Map.vue'
export default {
    name: "Detail",
    data: () => ({
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        property: [],
        schools: [],
        locations: [],
        propertiesLoaded: false
    }),
    components: {
        Map
    },
    created() {
        var x = document.getElementById("search");
        x.style.display = "none";
        this.fetchProperty();
    },
    methods: {
        fetchProperty: function () {
            fetch("/api/properties/get", {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                method: "POST",
                body: JSON.stringify({
                    id: this.$route.params.id
                })
            })
                .then(response => response.json())
                .then(data => {
                    this.property= JSON.parse(JSON.stringify(data.property));
                    this.schools = JSON.parse(JSON.stringify(data.schools));
                    this.locations[0] = this.property;
                    this.propertiesLoaded = true;
                })
                .catch(error => {  });
        }
    }

}
</script>

<style scoped>

</style>
