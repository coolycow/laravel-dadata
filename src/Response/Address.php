<?php

declare(strict_types=1);

namespace Coolycow\Dadata\Response;

class Address extends AbstractResponse
{
    /**
     * внутри МКАД (Москва).
     */
    public const BELTWAY_HIT_IN_MKAD = 'IN_MKAD';

    /**
     * За МКАД (Москва или Московская область).
     */
    public const BELTWAY_HIT_OUT_MKAD = 'OUT_MKAD';

    /**
     * Внутри КАД (Санкт-Петербург).
     */
    public const BELTWAY_HIT_IN_KAD = 'IN_KAD';

    /**
     * За КАД (Санкт-Петербург или Ленинградская область).
     */
    public const BELTWAY_HIT_OUT_KAD = 'OUT_KAD';

    /**
     * Страна.
     */
    public const FIAS_COUNTRY = 0;

    /**
     * Регион.
     */
    public const FIAS_REGION = 1;

    /**
     * Район.
     */
    public const FIAS_AREA = 3;

    /**
     * Город.
     */
    public const FIAS_CITY = 4;

    /**
     * Населенный пункт.
     */
    public const FIAS_SETTLEMENT = 6;

    /**
     * Улица.
     */
    public const FIAS_STREET = 7;

    /**
     * Дом.
     */
    public const FIAS_HOUSE = 8;

    /**
     * Иностранный или пустой.
     */
    public const FIAS_UNKNOWN = -1;

    /**
     * Центр района (Московская обл, Одинцовский р-н, г Одинцово).
     */
    public const CAPITAL_MARKER_AREA_CENTER = 1;

    /**
     * Центр региона (Новосибирская обл, г Новосибирск).
     */
    public const CAPITAL_MARKER_REGION_CENTER = 2;

    /**
     * Центр района и региона (Костромская обл, Костромской р-н, г Кострома).
     */
    public const CAPITAL_MARKER_AREA_AND_REGION_CENTER = 3;

    /**
     * Ни то, ни другое (Московская обл, г Балашиха).
     */
    public const CAPITAL_MARKER_NONE = 0;

    /**
     * Точные координаты.
     */
    public const QC_GEO_EXACT = 0;

    /**
     * Ближайший дом.
     */
    public const QC_GEO_CLOSEST_HOUSE = 1;

    /**
     * Улица.
     */
    public const QC_GEO_STREET = 2;

    /**
     * Населенный пункт.
     */
    public const QC_GEO_SETTLEMENT = 3;

    /**
     * Город.
     */
    public const QC_GEO_CITY = 4;

    /**
     * Координаты не определены.
     */
    public const QC_GEO_UNKNOWN = 5;

    /**
     * Пригоден для почтовой рассылки.
     */
    public const QC_COMPLETE_OK = 0;

    /**
     * Не пригоден, нет региона.
     */
    public const QC_COMPLETE_NO_REGION = 1;

    /**
     * Не пригоден, нет города.
     */
    public const QC_COMPLETE_NO_CITY = 2;

    /**
     * Не пригоден, нет улицы.
     */
    public const QC_COMPLETE_NO_STREET = 3;

    /**
     * Не пригоден, нет дома.
     */
    public const QC_COMPLETE_NO_HOUSE = 4;

    /**
     * Пригоден для юридических лиц или частных владений (нет квартиры).
     */
    public const QC_COMPLETE_NO_FLAT = 5;

    /**
     * Не пригоден.
     */
    public const QC_COMPLETE_BAD = 6;

    /**
     * Иностранный адрес.
     */
    public const QC_COMPLETE_FOREIGN = 7;

    /**
     * До почтового отделения (абонентский ящик или адрес до востребования).
     * Подходит для писем, но не для курьерской доставки.
     */
    public const QC_COMPLETE_POST_OFFICE = 8;

    /**
     * Пригоден, но низкая вероятность успешной доставки (дом не найден в ФИАС).
     */
    public const QC_COMPLETE_LOW = 10;

    /**
     * Дом найден в ФИАС по точному совпадению.
     */
    public const QC_HOUSE_HIGH = 2;

    /**
     * В ФИАС найден похожий дом; различие в литере, корпусе или строении.
     */
    public const QC_HOUSE_MEDIUM_SIMILAR = 3;

    /**
     * Дом найден в ФИАС по диапазону.
     */
    public const QC_HOUSE_MEDIUM_RANGE = 4;

    /**
     * Дом не найден в ФИАС.
     */
    public const QC_HOUSE_LOW = 10;

    /** @var string|null Исходный адрес одной строкой. */
    public ?string $source = null;

    /** @var string|null Стандартизованный адрес одной строкой. */
    public ?string $result = null;

    /** @var string|null Индекс. */
    public ?string $postal_code = null;

    /** @var string|null Страна. */
    public ?string $country = null;

    /** @var string|null Страна iso alfa2. */
    public ?string $country_iso_code = null;

    /** @var string|null Федеральный округ. */
    public ?string $federal_district = null;

    /** @var string|null Код ФИАС региона. */
    public ?string $region_fias_id = null;

    /** @var string|null Код КЛАДР региона. */
    public ?string $region_kladr_id = null;

    /** @var string|null Регион с типом. */
    public ?string $region_with_type = null;

    /** @var string|null Тип региона (сокращенный). */
    public ?string $region_type = null;

    /** @var string|null Тип региона. */
    public ?string $region_type_full = null;

    /** @var string|null Регион. */
    public ?string $region = null;

    /** @var string|null Код ФИАС района в регионе. */
    public ?string $area_fias_id = null;

    /** @var string|null Код КЛАДР района в регионе. */
    public ?string $area_kladr_id = null;

    /** @var string|null Район в регионе с типом. */
    public ?string $area_with_type = null;

    /** @var string|null Тип района в регионе (сокращенный). */
    public ?string $area_type = null;

    /** @var string|null Тип района в регионе. */
    public ?string $area_type_full = null;

    /** @var string|null Район в регионе. */
    public ?string $area = null;

    /** @var string|null Код ФИАС города. */
    public ?string $city_fias_id = null;

    /** @var string|null Код КЛАДР города. */
    public ?string $city_kladr_id = null;

    /** @var string|null Город с типом. */
    public ?string $city_with_type = null;

    /** @var string|null Тип города (сокращенный). */
    public ?string $city_type = null;

    /** @var string|null Тип города. */
    public ?string $city_type_full = null;

    /** @var string|null Город. */
    public ?string $city = null;

    /** @var string|null Административный округ (только для Москвы). */
    public ?string $city_area = null;

    /** @var string|null Код ФИАС района города (не заполняется). */
    public ?string $city_district_fias_id = null;

    /** @var string|null Код КЛАДР района города (не заполняется). */
    public ?string $city_district_kladr_id = null;

    /** @var string|null Район города с типом. */
    public ?string $city_district_with_type = null;

    /** @var string|null Тип района города (сокращенный). */
    public ?string $city_district_type = null;

    /** @var string|null Тип района города. */
    public ?string $city_district_type_full = null;

    /** @var string|null Район города. */
    public ?string $city_district = null;

    /** @var string|null Код ФИАС нас. пункта. */
    public ?string $settlement_fias_id = null;

    /** @var string|null Код КЛАДР нас. пункта. */
    public ?string $settlement_kladr_id = null;

    /** @var string|null Населенный пункт с типом. */
    public ?string $settlement_with_type = null;

    /** @var string|null Тип населенного пункта (сокращенный). */
    public ?string $settlement_type = null;

    /** @var string|null Тип населенного пункта. */
    public ?string $settlement_type_full = null;

    /** @var string|null Населенный пункт. */
    public ?string $settlement = null;

    /** @var string|null Код ФИАС улицы. */
    public ?string $street_fias_id = null;

    /** @var string|null Код КЛАДР улицы. */
    public ?string $street_kladr_id = null;

    /** @var string|null Улица с типом. */
    public ?string $street_with_type = null;

    /** @var string|null Тип улицы (сокращенный). */
    public ?string $street_type = null;

    /** @var string|null Тип улицы. */
    public ?string $street_type_full = null;

    /** @var string|null Улица. */
    public ?string $street = null;

    /** @var string|null Код ФИАС земельного участка. */
    public ?string $stead_fias_id = null;

    /** @var string|null Кадастровый номер участка. */
    public ?string $stead_cadnum = null;

    /** @var string|null Тип участка. */
    public ?string $stead_type = null;

    /** @var string|null Тип участка полностью. */
    public ?string $stead_type_full = null;

    /** @var string|null Участок. */
    public ?string $stead = null;

    /** @var string|null Код ФИАС дома. */
    public ?string $house_fias_id = null;

    /** @var string|null Код КЛАДР дома. */
    public ?string $house_kladr_id = null;

    /** @var string|null Кадастровый номер дома. */
    public ?string $house_cadnum = null;

    /** @var string|null Тип дома (сокращенный). */
    public ?string $house_type = null;

    /** @var string|null Тип дома. */
    public ?string $house_type_full = null;

    /** @var string|null Дом. */
    public ?string $house = null;

    /** @var string|null Тип корпуса/строения (сокращенный). */
    public ?string $block_type = null;

    /** @var string|null Тип корпуса/строения. */
    public ?string $block_type_full = null;

    /** @var string|null Корпус/строение. */
    public ?string $block = null;

    /** @var string|null Тип квартиры (сокращенный). */
    public ?string $flat_type = null;

    /** @var string|null Тип квартиры. */
    public ?string $flat_type_full = null;

    /** @var string|null Квартира. */
    public ?string $flat = null;

    /** @var float|null Площадь квартиры. */
    public ?float $flat_area = null;

    /** @var string|null Кадастровый номер квартиры. */
    public ?string $flat_cadnum = null;

    /** @var string|null Рыночная стоимость м². */
    public ?string $square_meter_price = null;

    /** @var string|null Рыночная стоимость квартиры. */
    public ?string $flat_price = null;

    /** @var string|null Тип комнаты. */
    public ?string $room_type = null;

    /** @var string|null Тип комнаты полностью. */
    public ?string $room_type_full = null;

    /** @var string|null Комната. */
    public ?string $room = null;

    /** @var string|null Абонентский ящик. */
    public ?string $postal_box = null;

    /** @var string|null Код ФИАС. */
    public ?string $fias_id = null;

    /** @var string|null Иерархический код адреса в ФИАС. */
    public ?string $fias_code = null;

    /** @var int|null Уровень детализации, до которого адрес найден в ФИАС (see FIAS_* constants). */
    public ?int $fias_level = null;

    /** @var string|null Код КЛАДР. */
    public ?string $kladr_id = null;

    /** @var string|null Идентификатор OpenStreetMap / GeoNames. */
    public ?string $geoname_id = null;

    /** @var int|null Является ли город центром (see CAPITAL_MARKER_* constants). */
    public ?int $capital_marker = null;

    /** @var string|null Код ОКАТО. */
    public ?string $okato = null;

    /** @var string|null Код ОКТМО. */
    public ?string $oktmo = null;

    /** @var string|null Код ИФНС для физических лиц. */
    public ?string $tax_office = null;

    /** @var string|null Код ИФНС для организаций (не заполняется). */
    public ?string $tax_office_legal = null;

    /** @var string|null Часовой пояс. */
    public ?string $timezone = null;

    /** @var float|null Координаты: широта. */
    public ?float $geo_lat = null;

    /** @var float|null Координаты: долгота. */
    public ?float $geo_lon = null;

    /** @var string|null Внутри кольцевой? (see BELTWAY_HIT_* constants). */
    public ?string $beltway_hit = null;

    /** @var float|null Расстояние от кольцевой в км. */
    public ?float $beltway_distance = null;

    /** @var string|null Подъезд. */
    public ?string $entrance = null;

    /** @var string|null Этаж. */
    public ?string $floor = null;

    /** @var int|null Код точности координат (see QC_GEO_* constants). */
    public ?int $qc_geo = null;

    /** @var int|null Код полноты (see QC_COMPLETE_* constants). */
    public ?int $qc_complete = null;

    /** @var int|null Признак наличия дома в ФИАС (see QC_HOUSE_* constants). */
    public ?int $qc_house = null;

    /** @var string|null Нераспознанная часть адреса. */
    public ?string $unparsed_parts = null;

    public function __toString(): string
    {
        return (string) $this->result;
    }
}
