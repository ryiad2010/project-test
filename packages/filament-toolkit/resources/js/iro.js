import iro from '@jaames/iro'
// /components/my-component.js
export default function colorPicker({
state,
width
}) {
	return {
		state,

		init() { 

        const picker = new iro.ColorPicker(this.$refs.picker, {
            ...(width ? {width}:{}),

            color: this.state,
        });
        picker.on('color:change', (color) => {
           this.state = color.hexString;
        });
		}
	}
}
