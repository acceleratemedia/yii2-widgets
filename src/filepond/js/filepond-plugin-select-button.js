/*!
 * FilePondPluginSelectButton 1.0.0
 * Licensed under MIT, https://opensource.org/licenses/MIT/
 * Please visit https://pqina.nl/filepond/ for details.
 */

/* eslint-disable */

(function(global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined'
    ? (module.exports = factory())
    : typeof define === 'function' && define.amd
    ? define(factory)
    : ((global = global || self), (global.FilePondPluginSelectButton = factory()));
})(this, function() {
  'use strict';

  var isPreviewableImage = function isPreviewableImage(file) {
    return /^image/.test(file.type);
  };

  /**
   * Image Select Proxy Plugin
   */
  var plugin = function plugin(_) {
    var addFilter = _.addFilter,
      utils = _.utils,
      views = _.views;
    var Type = utils.Type,
      createRoute = utils.createRoute,
      _utils$createItemAPI = utils.createItemAPI,
      createItemAPI =
        _utils$createItemAPI === void 0
          ? function(item) {
              return item;
            }
          : _utils$createItemAPI;
    var fileActionButton = views.fileActionButton;

    // called for each view that is created right after the 'create' method
    addFilter('CREATE_VIEW', function(viewAPI) {
      // get reference to created view
      var is = viewAPI.is,
        view = viewAPI.view,
        query = viewAPI.query;

      var canShowImagePreview = query('GET_ALLOW_IMAGE_PREVIEW');

      // only run for either the file or the file info panel
      var shouldExtendView =
        (is('file-info') && !canShowImagePreview) ||
        (is('file') && canShowImagePreview);

      if (!shouldExtendView) return;

      /**
       * Image Preview related
       */

      // create the image select plugin, but only do so if the item is an image
      var didLoadItem = function didLoadItem(_ref6) {
        var root = _ref6.root,
          props = _ref6.props;

        var id = props.id;

        // try to access item
        var item = query('GET_ITEM', id);
        if (!item) return;

        // handle interactions
        root.ref.handleSelect = function(e) {
          e.stopPropagation();
          root.element.dispatchEvent(
            new CustomEvent('FilePond:fileselected', {
              // event info
              detail: { id: id, serverId: item.serverId },

              // event behaviour
              bubbles: true,
              cancelable: true,
              composed: true // triggers listeners outside of shadow root
            })
          );

          // --- I don't think this is necessary but I'm leavint it because I saw it in the other plugin
          root.dispatch('SELECT_ITEM', { id: id, serverId: item.serverId });
        };

        if (canShowImagePreview) {
          // add select button to preview
          var buttonView = view.createChildView(fileActionButton, {
            label: 'select',
            icon: query('GET_SELECT_BUTTON_ICON_SELECT'),
            opacity: 0,
          });

          // select item classname
          buttonView.element.classList.add('filepond--action-select-item');
          buttonView.element.dataset.align = query(
            'GET_STYLE_SELECT_BUTTON_BUTTON_SELECT_POSITION'
          );
          buttonView.on('click', root.ref.handleSelect);
          root.ref.buttonSelectItem = view.appendChildView(buttonView);
        } else {
          // view is file info
          var filenameElement = view.element.querySelector(
            '.filepond--file-info-main'
          );
          var selectButton = document.createElement('button');
          selectButton.className = 'filepond--action-select-item-alt';
          selectButton.innerHTML =
            query('GET_SELECT_BUTTON_ICON_SELECT') + '<span>select</span>';
          selectButton.addEventListener('click', root.ref.handleSelect);
          filenameElement.appendChild(selectButton);

          root.ref.selectButton = selectButton;
        }
      };

      view.registerDestroyer(function(_ref7) {
        var root = _ref7.root;
        if (root.ref.buttonSelectItem) {
          root.ref.buttonSelectItem.off('click', root.ref.handleSelect);
        }
        if (root.ref.selectButton) {
          root.ref.selectButton.removeEventListener('click', root.ref.handleSelect);
        }
      });

      var didSelectItem = function(e){console.log("e",e)};
      var routes = {
        DID_LOAD_ITEM: didLoadItem,
        DID_SELECT_ITEM: didSelectItem
      };

      if (canShowImagePreview) {
        // displays the select button when the preview is updated
        var didPreviewUpdate = function didPreviewUpdate(_ref8) {
          var root = _ref8.root;
          if (!root.ref.buttonSelectItem) return;
          root.ref.buttonSelectItem.opacity = 1;
        };

        routes.DID_IMAGE_PREVIEW_SHOW = didPreviewUpdate;
      } else {
      }

      // start writing
      view.registerWriter(createRoute(routes));
    });

    // Expose plugin options
    return {
      options: {
        // location of processing button
        styleSelectButtonButtonSelectPosition: ['bottom left', Type.STRING],
        // the icon to use for the select button
        selectButtonIconSelect: [
          '<svg width="26" height="26" viewBox="0 0 26 26" xmlns="http://www.w3.org/2000/svg"><path d="M18.293 9.293a1 1 0 0 1 1.414 1.414l-7.002 7a1 1 0 0 1-1.414 0l-3.998-4a1 1 0 1 1 1.414-1.414L12 15.586l6.294-6.293z" fill="currentColor" fill-rule="nonzero"/></svg>',
          Type.STRING
        ],
      }
    };
  };

  // fire pluginloaded event if running in browser, this allows registering the plugin when using async script tags
  var isBrowser =
    typeof window !== 'undefined' && typeof window.document !== 'undefined';
  if (isBrowser) {
    document.dispatchEvent(
      new CustomEvent('FilePond:pluginloaded', { detail: plugin })
    );
  }

  return plugin;
});