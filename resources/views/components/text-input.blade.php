<label>
    <span>{{ $getlabel() }}</span>
    <input type="text" maxlength="{{ $getMaxLength() }}" wire:model.live="{{ $getName() }}" />
</label>
