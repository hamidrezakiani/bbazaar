<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

class SuggestionItemsResource extends JsonResource
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
            'id' => $this['id'],
            'title'	=> $this['title'],
            'badge'	 => $this['badge'],
            "selling" => $this['selling'] ? ($request->header('currency') ?? 'AFN') == 'AFN' ? $this['selling'] : (number_format($this['selling'] / getDollar()->price, 2, '.' , '') ) : $this['selling']  ,
            "offered" => $this['offered'] ? ($request->header('currency') ?? 'AFN') == 'AFN' ? $this['offered'] : ( number_format($this['offered'] / getDollar()->price , 2, '.' , '')) : $this['offered'],
            'slug' => $this['slug'],
            'image'	=> $this['image'],
            'review_count' => $this['review_count'],
            'rating' => $this['rating'],
            "price" => ($request->header('currency') ?? 'AFN') == 'AFN' ? $this->minPrice($this['selling'],$this['offered'],$this['price']) : ($this->minPrice($this['selling'],$this['offered'],$this['price']) ? (number_format($this->minPrice($this['selling'],$this['offered'],$this['price']) / getDollar()->price, 2, '.', '')): ''),
            'end_time' => $this['end_time']
        ];
    }

    private function minPrice($selling,$offered,$price){
        return min($selling,$offered,$price);
    }
}