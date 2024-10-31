(function() {
    tinymce.create("tinymce.plugins.quabads_btn_plugin", {

        init: function(ed, url){
      ed.addButton('green', {
        title: 'QuabAds Shortcode',
        cmd: 'myBlockquoteBtnCmd',
        image: url + '/icon.png'
      });
      ed.addCommand('myBlockquoteBtnCmd', function(){
        var selectedText = ed.selection.getContent({format: 'html'});
        var win = ed.windowManager.open({
          title: 'Ad-Slot Properties',
          body: [
            {
              type: 'listbox',
              name: 'author',
              label: 'Slot size',
              minWidth: 300,
              values : [
					{ text: 'Banner (468x60)', value: 'banner' },
				  	{ text: 'Skyscraper (120x600)', value: 'skyscraper' },
				  	{ text: 'Wide Skyscraper (160x600)', value: 'wide_skyscraper' },
				  	{ text: 'Half-Page Ad (300x600)', value: 'half_page' },
					{ text: 'Leaderboard (768x90)', value: 'leaderboard' },
					{ text: 'Large Leaderboard (970x90)', value: 'large_leaderboard' },
					{ text: 'Small Square (200x200)', value: 'small_square' },
				  	{ text: 'Square (250x250)', value: 'square' },
				  	{ text: 'Large Mobile Leaderboard (300x100)', value: 'large_mobile_leaderboard' },
				  	{ text: 'Inline Rectangle (300x250)', value: 'inline_rectangle' },
				  	{ text: 'Billboard (970x250)', value: 'billboard' },
					{ text: 'Large Rectangle (336x280)', value: 'large_rectangle', selected: true }
				  ]
			}
          ],
          buttons: [
            {
              text: "Ok",
              subtype: "primary",
              onclick: function() {
                win.submit();
              }
            },
            {
              text: "Skip",
              onclick: function() {
                win.close();
                var returnText = ' ' + selectedText + ' ';
                ed.execCommand('mceInsertContent', 0, returnText);
              }
            },
            {
              text: "Cancel",
              onclick: function() {
                win.close();
              }
            }
          ],
          onsubmit: function(e){
            var params = [];
            if( e.data.author.length > 0 ) {
              params.push('author="' + e.data.author + '"');
            }
            var returnText = '[quabads size="'+ e.data.author +'"]';
            ed.execCommand('mceInsertContent', 0, returnText);
          }
        });
      });
    },
    getInfo: function() {
      return {
        longname : 'QuabAds Ad-Slot Buttons',
        author : 'QuabAds',
        authorurl : 'https://www.quabads.com',
        version : "1.2.1"
      };
    }
    });

    tinymce.PluginManager.add("quabads_btn_plugin", tinymce.plugins.quabads_btn_plugin);
})();