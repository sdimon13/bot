<?php

namespace App\Services;

use App\Jobs\ObjectsEventsSendingJob;
use App\Models\Event;
use App\Models\ObjectsEvent;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ObjectsEventService
{
    public function makeObjectEvent(Event $event)
    {
        $objectsEvent = [
            'object_id' => $event->object->ID,
            'event_id' => $event->ID,
            'event_datetime' => Carbon::parse($event->E_DATETIME),
            'object_name' => $event->object->NAME,
            'object_number' => $event->object->entity->STR_IDENT,
            'description_name' => $event->description->ED_NAME,
            'description_comment' => $event->ADDITIONAL_DESCRIPTION,
            'zone_name' => $event->zone->NAME,
            'device_type' => $event->device->type->DEVICE_TYPE,
            'device_name' => $event->device->DEV_NAME,
        ];

        foreach ($event->object->rows as $row) {
            foreach ($row->refferenceRow->cells as $cell) {
                $columns = $cell->refferenceCell->columns ?? [];
                foreach ($columns as $column) {
                    if (!is_null($cell->refferenceCell->CELL_VALUE_BLOB)) {
                        $columnTitle = $column->refferenceColumn->COLUMN_TITLE;

                        if ($this->checkColumnForAddress($columnTitle)) {
                            $columnTitle = 'Адрес';
                            $objectsEvent['address'] = $row->refferenceRow->STRCACHE;
                        }

                        if ($this->checkColumnForContact($columnTitle)) {
                            $columnTitle = 'Контактные лица';
                            $objectsEvent['contacts'][] = $row->refferenceRow->STRCACHE;
                        }

                        $result[] = [
                            'row_value' => $row->refferenceRow->STRCACHE,
                            'row_entity' => $row->refferenceRow->ENTITY_ID,
                            'cell_value' => $cell->refferenceCell->CELL_VALUE_BLOB,
                            'column_name' => $column->refferenceColumn->COLUMN_NAME,
                            'column_value' => $columnTitle,
                        ];

                        if ($this->checkColumnForAddress($columnTitle)) {
                            break 2;
                        }

                        if ($this->checkColumnForContact($columnTitle)) {
                            break 2;
                        }
                    }
                }
            }
        }
        try {
            $model = ObjectsEvent::fields(array_keys($objectsEvent))->create($objectsEvent);
        } catch (ValidationException $exception) {
            return;
        }


        ObjectsEventsSendingJob::dispatch($model->id);
    }

    private function checkColumnForAddress(string $columnTitle) : bool
    {
        if (
            in_array(
                $columnTitle,
                [
                    'Адрес', 'Область', 'Город/Поселок', 'Район', 'Улица', 'Дом'
                ]
            )
        ) {
            return true;
        }
        return false;
    }

    private function checkColumnForContact(string $columnTitle) : bool
    {
        if (
            in_array(
                $columnTitle,
                [
                    'Контактные лица', 'Фамилия'
                ]
            )
        ) {
            return true;
        }
        return false;
    }
}