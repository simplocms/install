<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<browserconfig>
    <msapplication>
        <tile>
            @foreach($icons as $icon)
                <{{$icon['element']}} src="{{ $icon['src'] }}"/>
            @endforeach
            <TileColor>{{ $color }}</TileColor>
        </tile>
    </msapplication>
</browserconfig>
