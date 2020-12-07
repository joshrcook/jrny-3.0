const plugin = require('tailwindcss/plugin')

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
				black: {
					DEFAULT: '#161C2D',
				}
			},
			spacing: {
				7.5: '1.875rem',
				15: '3.75rem',
				25: '6.25rem',
			},
		},
	},
	variants: {
		extend: {},
	},
	plugins: [
		plugin(function({ addVariant }) {
			addVariant('important', ({ container }) => {
			  container.walkRules(rule => {
				rule.selector = `.\\!${rule.selector.slice(1)}`
				rule.walkDecls(decl => {
				  decl.important = true
				})
			  })
			})
		  })
	],
	corePlugins: {
		container: false,
	}
};
