<?php

namespace App\Http\Resources\Mobile;

use App\Http\Resources\Mobile\SuggestionItemCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class SuggestedProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'suggestion_1' => new SuggestionItemCollection($this['suggestion_1']),
            'suggestion_2' => new SuggestionItemCollection($this['suggestion_2'])
        ];
    }
}