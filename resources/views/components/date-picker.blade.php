<input
        x-data
        x-ref="input"
        x-init="new Pikaday({
        field: $refs.input,
        onSelect: function(date) {
            $dispatch('input', this.getMoment().format('YYYY-MM-DD'))
        }
        })"
        type="text"
        {{ $attributes }}
>