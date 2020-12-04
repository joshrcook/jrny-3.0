module.exports = {
	purge: [],
	darkMode: false, // or 'media' or 'class'
	theme: {
		screens: {
			xs: '0px',
			sm: '576px',
			md: '768px',
			lg: '992px',
			xl: '1200px',
		},
		fontFamily: {
			sans: "Inter, sans-serif",
		},
		extend: {
			colors: {
				primary: {
					DEFAULT: "#F64B4B",
				},
			},
		},
	},
	variants: {
		extend: {},
	},
	plugins: [],
};
