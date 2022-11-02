<template>
    <div style="height: 1000px">
        <div id="map" class="map" style="width: 100%; height: 1000px; border: 2px solid black; background-color: white"></div>
        <!---
        <form>
            <label for="type">Geometry type &nbsp;</label>
            <select id="type">
                <option value="Point">Point</option>
                <option value="LineString">LineString</option>
                <option value="Polygon">Polygon</option>
                <option value="Circle">Circle</option>
            </select>
        </form>-->
    </div>
</template>

<script>

import Map from 'ol/Map';
import View from 'ol/View';
import {Draw, Modify, Snap} from 'ol/interaction';
import {OSM, Vector as VectorSource} from 'ol/source';
import {Tile as TileLayer, Vector as VectorLayer} from 'ol/layer';
import {get} from 'ol/proj';
import * as Geom from 'ol/geom';
import * as ol from 'ol';
import * as Proj from 'ol/proj';
import * as Style from 'ol/style';

export default {
    props: {
        locations: Array,
    },
    name: "Map",
    data(){
        return {
            isDisplay: false,
            gdprForm: {
                athenaId: "",
                platformId: "",
                email: "",
            },
            searchesData : [],
            clicksData : [],
            clicksOverTimeData : [],
            ordersData : [],
            customerValueData : [],
            currentWebsite : '',
            dbConn : '',
            warningMessage: '',
            title: ''
        }
    },
    mounted() {
        var ref = this;

        const raster = new TileLayer({
            source: new OSM(),
        });

        const source = new VectorSource();
        const vector = new VectorLayer({
            source: source,
            style: {
                'fill-color': 'rgba(255, 255, 255, 0.2)',
                'stroke-color': '#ffcc33',
                'stroke-width': 2,
                'circle-radius': 7,
                'circle-fill-color': '#ffcc33',
            },
        });

        const extent = get('EPSG:3857').getExtent().slice();
        extent[0] += extent[0];
        extent[2] += extent[2];
        const map = new Map({
            layers: [raster, vector],
            target: 'map',
            view: new View({
                center: [-13000000, 3600000],
                zoom: 7,
                extent,
            }),
        });
        var vsource = new VectorSource();
        this.locations.forEach(function (el) {
            console.log(el.location[0]);
            var marker = new ol.Feature({
                type: 'icon',
                geometry: new Geom.Point(Proj.fromLonLat(el.location))
            });
            marker.setStyle(
                new Style.Style({
                    text: new Style.Text({
                        text: '-',
                        scale: 1.2,
                        textAlign: "end"
                    }),
                    image: new Style.Icon({
                        src: 'https://maps.google.com/mapfiles/kml/paddle/red-blank.png',
                        scale: 0.3
                    }),

                })
            );
            vsource.addFeature(marker);
        })

        let vlayer = new VectorLayer({
            source: vsource
        });
        map.addLayer(vlayer);

        const modify = new Modify({source: source});
        map.addInteraction(modify);

        let draw, snap; // global so we can remove them later
        const typeSelect = document.getElementById('type');

        /*function addInteractions() {
            draw = new Draw({
                source: source,
                type: typeSelect.value,
            });
            draw.on('drawend',function(e){
                let typeSearch = document.getElementById('type').value;
                ref.drawSearch(typeSearch, e.feature.getGeometry().getCoordinates());
               // this.$emit('eventname', this.locations)

            })
            map.addInteraction(draw);
            snap = new Snap({source: source});
            map.addInteraction(snap);
        }

        typeSelect.onchange = function () {
            map.removeInteraction(draw);
            map.removeInteraction(snap);
            addInteractions();
        };

        addInteractions();*/

    },
    methods: {
        drawSearch (type, coordinates) {
            console.log(type);
            //this.drawSearch(typeSearch, e.feature.getGeometry().getCoordinates()[0]);
            //Proj.toLonLat(e.feature.getGeometry().getCoordinates()[0]);
            this.$emit('eventname', this.locations)

        }
    }
}
</script>

<style scoped>

</style>
