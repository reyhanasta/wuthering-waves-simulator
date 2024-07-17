<div>

<div class="container">
    <div class="gachaResult">
        <ul> 
            @foreach ($gachaResults as $item)
            <div id="weapon-list" class="relative p-2 overflow-hidden bg-gray-800 shadow-lg rounded-xl">
                <div class="absolute top-0 left-0 p-1 text-xs font-bold text-white bg-yellow-500">New</div>
                <img class="object-cover w-full h-32" src="{{$item['img']}}" alt="{{$item['name']}}" style="background-color: purple"/>
                <div class="p-2">
                    <div class="flex justify-center">
                        <span class="text-xs text-yellow-400">
                           
                                {{$item['rarity'] === 1 ? '★★★★★' : ''}} 
                                {{$item['rarity'] === 2 ? '★★★★' :   ''}} 
                                {{$item['rarity'] === 3 ? '★★★' :  ''}} 
                              
                        </span>
                    </div>
                    <div class="mt-2 text-center">
                        <p class="text-white">{{$item['name']}}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </ul>
       
    </div>
   
</div>

    <img src="#" alt="Gacha Banner">
   

    <button type="button" wire:click="singlePull">Gacha</button>
    <button type="button" wire:click="tenPulls">Gacha 10x</button>
</div>
