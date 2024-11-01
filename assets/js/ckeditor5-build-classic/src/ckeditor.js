/**
 * @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
 */

// The editor creator to use.
import ClassicEditorBase from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';

import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import UploadAdapter from '@ckeditor/ckeditor5-adapter-ckfinder/src/uploadadapter';
import Autoformat from '@ckeditor/ckeditor5-autoformat/src/autoformat';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import BlockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote';
import CKFinder from '@ckeditor/ckeditor5-ckfinder/src/ckfinder';
import EasyImage from '@ckeditor/ckeditor5-easy-image/src/easyimage';
import Heading from '@ckeditor/ckeditor5-heading/src/heading';
import Image from '@ckeditor/ckeditor5-image/src/image';
import ImageCaption from '@ckeditor/ckeditor5-image/src/imagecaption';
import ImageStyle from '@ckeditor/ckeditor5-image/src/imagestyle';
import ImageToolbar from '@ckeditor/ckeditor5-image/src/imagetoolbar';
import ImageUpload from '@ckeditor/ckeditor5-image/src/imageupload';
import Indent from '@ckeditor/ckeditor5-indent/src/indent';
import Link from '@ckeditor/ckeditor5-link/src/link';
import List from '@ckeditor/ckeditor5-list/src/list';
import MediaEmbed from '@ckeditor/ckeditor5-media-embed/src/mediaembed';
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph';
import PasteFromOffice from '@ckeditor/ckeditor5-paste-from-office/src/pastefromoffice';
import Table from '@ckeditor/ckeditor5-table/src/table';
import TableToolbar from '@ckeditor/ckeditor5-table/src/tabletoolbar';
import TextTransformation from '@ckeditor/ckeditor5-typing/src/texttransformation';
import CloudServices from '@ckeditor/ckeditor5-cloud-services/src/cloudservices';
import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';
import FontFamily from '@ckeditor/ckeditor5-font/src/font';
import FontBackgroundColor from '@ckeditor/ckeditor5-font/src/font';
import FontSize from '@ckeditor/ckeditor5-font/src/font';
import FontColor from '@ckeditor/ckeditor5-font/src/font';

import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import {
	toWidget,
	viewToModelPositionOutsideModelElement,
} from '@ckeditor/ckeditor5-widget/src/utils';
import Widget from '@ckeditor/ckeditor5-widget/src/widget';
import Command from '@ckeditor/ckeditor5-core/src/command';

import {
	addListToDropdown,
	createDropdown,
} from '@ckeditor/ckeditor5-ui/src/dropdown/utils';
import Collection from '@ckeditor/ckeditor5-utils/src/collection';
import Model from '@ckeditor/ckeditor5-ui/src/model';

//import Placeholder from 'ckeditor5-placeholder';

export default class ClassicEditor extends ClassicEditorBase {}

class Placeholder extends Plugin {
	static get requires() {
		return [PlaceholderEditing, PlaceholderUI];
	}
}

class PlaceholderCommand extends Command {
	execute({ value }) {
		const editor = this.editor;
		const selection = editor.model.document.selection;

		editor.model.change((writer) => {
			// Create a <placeholder> elment with the "name" attribute (and all the selection attributes)...
			const placeholder = writer.createElement('placeholder', {
				...Object.fromEntries(selection.getAttributes()),
				name: value,
			});

			// ... and insert it into the document.
			editor.model.insertContent(placeholder);

			// Put the selection on the inserted element.
			writer.setSelection(placeholder, 'on');
		});
	}

	refresh() {
		const model = this.editor.model;
		const selection = model.document.selection;

		const isAllowed = model.schema.checkChild(
			selection.focus.parent,
			'placeholder'
		);

		this.isEnabled = isAllowed;
	}
}

class PlaceholderUI extends Plugin {
	init() {
		const editor = this.editor;
		const t = editor.t;
		const placeholderNames = editor.config.get('placeholderConfig.types');

		// The "placeholder" dropdown must be registered among the UI components of the editor
		// to be displayed in the toolbar.
		editor.ui.componentFactory.add('placeholder', (locale) => {
			const dropdownView = createDropdown(locale);

			// Populate the list in the dropdown with items.
			addListToDropdown(
				dropdownView,
				getDropdownItemsDefinitions(placeholderNames)
			);

			dropdownView.buttonView.set({
				// The t() function helps localize the editor. All strings enclosed in t() can be
				// translated and change when the language of the editor changes.
				label: t('Placeholder'),
				tooltip: true,
				withText: true,
			});

			// Disable the placeholder button when the command is disabled.
			const command = editor.commands.get('placeholder');
			dropdownView.bind('isEnabled').to(command);

			// Execute the command when the dropdown item is clicked (executed).
			this.listenTo(dropdownView, 'execute', (evt) => {
				editor.execute('placeholder', {
					value: evt.source.commandParam,
				});
				editor.editing.view.focus();
			});

			return dropdownView;
		});
	}
}

function getDropdownItemsDefinitions(placeholderNames) {
	const itemDefinitions = new Collection();

	for (const name of placeholderNames) {
		const definition = {
			type: 'button',
			model: new Model({
				commandParam: name,
				label: name,
				withText: true,
			}),
		};

		// Add the item definition to the collection.
		itemDefinitions.add(definition);
	}

	return itemDefinitions;
}

class PlaceholderEditing extends Plugin {
	static get requires() {
		return [Widget];
	}

	init() {

		this._defineSchema();
		this._defineConverters();

		this.editor.commands.add(
			'placeholder',
			new PlaceholderCommand(this.editor)
		);

		this.editor.editing.mapper.on(
			'viewToModelPosition',
			viewToModelPositionOutsideModelElement(
				this.editor.model,
				(viewElement) => viewElement.hasClass('placeholder')
			)
		);
		this.editor.config.define('placeholderConfig', {
			types: [
				'customer_name',
				'order_no',
				'order_date',
				'blog_name',
				'user_name',
				'user_email',
				'post_title',
				'permalink',
				'comment_author',
				'comment_author_IP',
				'comment_author_domain',
				'comment_author_url',
				'comment_author_email',
				'comment_content',
				'approve_url',
				'trash_url',
				'spam_url',
				'moderation_url',
				'comments_counts',
				'login_url',
				'password',
				'site_url',
				'admin_email',
				'IPaddress',
				'customer_note',
				'admin_name',
				'first_name',
				'resetpassword_url',
			],
		});
	}

	_defineSchema() {
		const schema = this.editor.model.schema;

		schema.register('placeholder', {
			// Allow wherever text is allowed:
			allowWhere: '$text',

			// The placeholder will act as an inline node:
			isInline: true,

			// The inline widget is self-contained so it cannot be split by the caret and it can be selected:
			isObject: true,

			// The inline widget can have the same attributes as text (for example linkHref, bold).
			allowAttributesOf: '$text',

			// The placeholder can have many types, like date, name, surname, etc:
			allowAttributes: ['name'],
		});
	}

	_defineConverters() {
		const conversion = this.editor.conversion;

		conversion.for('upcast').elementToElement({
			view: {
				name: 'span',
				classes: ['placeholder'],
			},
			model: (viewElement, { writer: modelWriter }) => {
				// Extract the "name" from "{name}".
				const name = viewElement.getChild(0).data.slice(1, -1);

				return modelWriter.createElement('placeholder', { name });
			},
		});

		conversion.for('editingDowncast').elementToElement({
			model: 'placeholder',
			view: (modelItem, { writer: viewWriter }) => {
				const widgetElement = createPlaceholderView(
					modelItem,
					viewWriter
				);

				// Enable widget handling on a placeholder element inside the editing view.
				return toWidget(widgetElement, viewWriter);
			},
		});

		conversion.for('dataDowncast').elementToElement({
			model: 'placeholder',
			view: (modelItem, { writer: viewWriter }) =>
				createPlaceholderView(modelItem, viewWriter),
		});

		// Helper method for both downcast converters.
		function createPlaceholderView(modelItem, viewWriter) {
			const name = modelItem.getAttribute('name');

			const placeholderView = viewWriter.createContainerElement(
				'span',
				{
					class: 'placeholder',
				},
				{
					isAllowedInsideAttributeElement: true,
				}
			);

			// Insert the placeholder name (as a text).
			const innerText = viewWriter.createText('{' + name + '}');
			viewWriter.insert(
				viewWriter.createPositionAt(placeholderView, 0),
				innerText
			);

			return placeholderView;
		}
	}
}

ClassicEditor.create(document.querySelector('#editor'), {
	plugins: [Essentials, Paragraph, Heading, List, Bold, Italic, Placeholder],
	toolbar: [
		'heading',
		'|',
		'bold',
		'italic',
		'numberedList',
		'bulletedList',
		'|',
		'placeholder',
	],
	placeholderConfig: {
		types: [
			'customer_name',
			'order_no',
			'order_date',
			'blog_name',
			'user_name',
			'user_email',
			'post_title',
			'permalink',
			'comment_author',
			'comment_author_IP',
			'comment_author_domain',
			'comment_author_url',
			'comment_author_email',
			'comment_content',
			'approve_url',
			'trash_url',
			'spam_url',
			'moderation_url',
			'comments_counts',
			'login_url',
			'password',
			'site_url',
			'admin_email',
			'IPaddress',
			'customer_note',
			'admin_name',
			'first_name',
			'resetpassword_url',
		],
	},
})
	.then((editor) => {

		// Expose for playing in the console.
		window.editor = editor;
	})
	.catch((error) => {
		console.error(error.stack);
	});

// Plugins to include in the build.
ClassicEditor.builtinPlugins = [
	Essentials,
	UploadAdapter,
	Autoformat,
	Bold,
	Italic,
	BlockQuote,
	CKFinder,
	CloudServices,
	EasyImage,
	Heading,
	Image,
	ImageCaption,
	ImageStyle,
	ImageToolbar,
	ImageUpload,
	Indent,
	Link,
	List,
	MediaEmbed,

	Paragraph,
	PasteFromOffice,
	Table,
	TableToolbar,
	TextTransformation,
	Alignment,
	FontFamily,
	FontSize,
	FontColor,
	FontBackgroundColor,
	Placeholder,
	//PlaceholderEditing,
	//PlaceholderUI
];

// Editor configuration.
ClassicEditor.defaultConfig = {
	toolbar: {
		items: [
			'heading',
			'|',
			'bold',
			'italic',
			'link',
			'bulletedList',
			'numberedList',
			'|',
			'indent',
			'outdent',
			'|',
			'imageUpload',
			'blockQuote',
			'insertTable',
			'mediaEmbed',
			'fontFamily',
			'fontColor',
			'fontSize',
			'fontBackgroundcolor',
			'undo',
			'redo',
			'placeholder',
			//			'placeholderEditing',
			//			'placeholderUI'
		],
	},
	placeholderProps: {
		types: ['First Name', 'Date'],
	},
	image: {
		toolbar: [
			'imageStyle:full',
			'imageStyle:side',
			'|',
			'imageTextAlternative',
		],
	},
	table: {
		contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'],
	},
	// This value must be kept in sync with the language defined in webpack.config.js.
	language: 'en',
};
