<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Form\Field;

class Address extends Field
{
    /**
     * Column name.
     *
     * @var array
     */
    protected $column = [];

    protected static $js = [
        '//map.qq.com/api/js?v=2.exp&key=QCMBZ-GJEC4-4WEUU-XLYLU-STYF5-XFBSV&libraries=place',
    ];

    /**
     * @var string
     */
    protected $view = 'admin.tools.address';


    /**
     * Latlong constructor.
     *
     * @param string $column
     * @param array $arguments
     */
    public function __construct($column, $arguments)
    {
        $this->column['lat'] = (string)$column;
        $this->column['lng'] = (string)$arguments[0];
        $this->column['address'] = (string)$arguments[2];
        array_shift($arguments);
        $this->label = $this->formatLabel($arguments);
        $this->id    = $this->formatId($this->column);
    }


    /**
     * {@inheritdoc}
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function render()
    {  $id=$this->id;
        $this->script =  <<<EOT
        (function() {
            var geocoder,map,marker,citylocation = null;
            function init(name) {
                var lat = $('#{$id['lat']}');
                var lng = $('#{$id['lng']}');
                var ads = $('#{$id['address']}');

                var center = new qq.maps.LatLng(lat.val(), lng.val());

                var container = document.getElementById("map_"+name);
                map = new qq.maps.Map(container, {
                    center: center,
                    zoom: 13
                });
                marker = new qq.maps.Marker({
                    position: center,
                    draggable: true,
                    map: map
                });
                //调用地址解析类
                geocoder = new qq.maps.Geocoder({
                    complete : function(result){
                        lat.val(result.detail.location.lat);
                        lng.val(result.detail.location.lng);
                        map.setCenter(result.detail.location);
                        marker.setPosition(result.detail.location);

                    }
                });

                if( ! lat.val() || ! lng.val()) {
                    citylocation = new qq.maps.CityService({
                        complete : function(result){
                            map.setCenter(result.detail.latLng);
                //设置marker标记

                            marker.setPosition(result.detail.latLng);
                        }
                    });

                    citylocation.searchLocalCity();
                }
                //添加点击
                qq.maps.event.addListener(map, 'click', function(event) {

                    var    geocoder = new qq.maps.Geocoder({
                        complete : function(result){
                            ads.val(result.detail.address);
                        }
                    });
                    geocoder.getAddress(event.latLng);
                    marker.setPosition(event.latLng);
                       lat.val(event.latLng.lat);
                    lng.val(event.latLng.lng);
                });

            

            }
            $('#search').click(function () {
                var address = document.getElementById("address").value;
            //通过getLocation();方法获取位置信息值
                geocoder.getLocation(address);
            })
            init('{$id['address']}');
        })();

EOT;

        return parent::render()->with(['height' =>300]);
    }
}
