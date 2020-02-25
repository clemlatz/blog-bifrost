/*global $, dotclear */
'use strict';

//~ metaEditor & metaEditor.prototype should go to the core.
function metaEditor(target, meta_field, meta_type, meta_options) {
  this.target = target;
  this.meta_field = meta_field;
  this.meta_type = meta_type;

  // options
  meta_options = meta_options || {};
  this.meta_url = meta_options.meta_url || this.meta_url;
  this.list_type = meta_options.list_type || this.list_type;
  this.text_confirm_remove = meta_options.text_confirm_remove || this.text_confirm_remove;
  this.text_add_meta = meta_options.text_add_meta || this.text_add_meta;
  this.text_choose = meta_options.text_choose || this.text_choose;
  this.text_all = meta_options.text_all || this.text_all;
  this.text_separation = meta_options.text_separation || this.text_separation;
}

metaEditor.prototype = {
  meta_url: '',
  text_confirm_remove: 'Are you sure you want to remove this %s?',
  text_add_meta: 'Add a %s to this entry',
  text_choose: 'Choose from list',
  text_all: 'all',
  text_separation: 'Separate each %s by comas',
  list_type: 'more',

  target: null,
  meta_type: null,
  meta_dialog: null,
  meta_field: null,
  submit_button: null,
  post_id: false,

  service_uri: 'services.php',

  displayMeta: function(type, post_id) {
    this.meta_type = type;
    this.post_id = post_id;
    this.target.empty();

    this.meta_dialog = $('<input type="text" class="ib" />');
    this.meta_dialog.attr('title', this.text_add_meta.replace(/%s/, this.meta_type));
    this.meta_dialog.attr('id', 'post_meta_input');
    // Meta dialog input
    this.meta_dialog.on('keypress', function(evt) { // We don't want to submit form!
      if (evt.keyCode == 13) {
        This.addMeta(this.value);
        return false;
      }
      return true;
    });

    const This = this;

    this.submit_button = $('<input type="button" value="ok" class="ib meta-helper" />');
    this.submit_button.on('click', function() {
      const v = This.meta_dialog.val();
      This.addMeta(v);
      return false;
    });

    this.addMetaDialog();

    if (this.post_id == false) {
      this.target.append(this.meta_field);
    }
    this.displayMetaList();
  },

  displayMetaList: function() {
    let li;
    if (this.meta_list == undefined) {
      this.meta_list = $('<ul class="metaList"></ul>');
      this.target.prepend(this.meta_list);
    }

    if (this.post_id == false) {
      const meta = this.splitMetaValues(this.meta_field.val());

      this.meta_list.empty();
      for (let i = 0; i < meta.length; i++) {
        li = $('<li>' + meta[i] + '</li>');
        const a_remove = $('<button type="button" class="metaRemove meta-helper"><img src="images/trash.png" alt="remove" /></button>');
        a_remove.get(0).caller = this;
        a_remove.get(0).meta_id = meta[i];
        a_remove.on('click', function() {
          this.caller.removeMeta(this.meta_id);
          return false;
        });
        li.prepend('&nbsp;').prepend(a_remove);
        this.meta_list.append(li);
      }
    } else {
      const This = this;
      const params = {
        f: 'getMeta',
        metaType: this.meta_type,
        sortby: 'metaId,asc',
        postId: this.post_id
      };

      $.get(this.service_uri, params, function(data) {
        data = $(data);

        if (data.find('rsp').attr('status') != 'ok') {
          return;
        }

        This.meta_list.empty();
        data.find('meta').each(function() {
          const meta_id = $(this).text();
          li = $('<li><a href="' + This.meta_url + $(this).attr('uri') + '">' + meta_id + '</a></li>');
          const a_remove = $('<button type="button" class="metaRemove meta-helper"><img src="images/trash.png" alt="remove" /></button>');
          a_remove.get(0).caller = This;
          a_remove.get(0).meta_id = meta_id;
          a_remove.on('click', function() {
            this.caller.removeMeta(this.meta_id);
            return false;
          });
          li.prepend('&nbsp;').prepend(a_remove);
          This.meta_list.append(li);
        });
      });
    }
  },

  addMetaDialog: function() {

    if (this.submit_button == null) {
      this.target.append($('<p></p>').append(this.meta_dialog));
    } else {
      this.target.append($('<p></p>').append(this.meta_dialog).append(' ').append(this.submit_button));
    }

    if (this.text_separation != '') {
      this.target.append($('<p></p>').addClass('form-note').append(this.text_separation.replace(/%s/, this.meta_type)));
    }

    this.showMetaList(this.list_type, this.target);

  },

  showMetaList: function(list_type, target) {
    const params = {
      f: 'getMeta',
      metaType: this.meta_type,
      sortby: 'metaId,asc'
    };

    if (list_type == 'more') {
      params.limit = '30';
    }

    const This = this;

    $.get(this.service_uri, params, function(data) {

      const pl = $('<p class="addMeta"></p>');

      $(target).find('.addMeta').remove();

      if ($(data).find('meta').length > 0) {
        pl.empty();

        $(data).find('meta').each(function(i) {
          const meta_link = $('<button type="button" class="metaItem meta-helper">' + $(this).text() + '</button>');
          meta_link.get(0).meta_id = $(this).text();
          meta_link.on('click', function() {
            const v = This.splitMetaValues(This.meta_dialog.val() + ',' + this.meta_id);
            This.meta_dialog.val(v.join(','));
            return false;
          });

          if (i > 0) {
            pl.append(', ');
          }
          pl.append(meta_link);
        });

        if (list_type == 'more') {
          const a_more = $('<button type="button" class="button metaGetMore meta-helper"></button>');
          a_more.append(This.text_all + String.fromCharCode(160) + String.fromCharCode(187));
          a_more.on('click', function() {
            This.showMetaList('more-all', target);
            return false;
          });
          pl.append(', ').append(a_more);
        }

        if (list_type != 'more-all') {
          pl.addClass('hide');

          const pa = $('<p></p>');
          target.append(pa);

          const a = $(`<button type="button" class="button metaGetList meta-helper">${This.text_choose}</button>`);
          a.on('click', function() {
            $(this).parent().next().removeClass('hide');
            $(this).remove();
            return false;
          });

          pa.append(a);
        }

        target.append(pl);

      } else {
        pl.empty();
      }
    });
  },

  addMeta: function(str) {
    str = this.splitMetaValues(str).join(',');
    if (this.post_id == false) {
      str = this.splitMetaValues(this.meta_field.val() + ',' + str);
      this.meta_field.val(str);

      this.meta_dialog.val('');
      this.displayMetaList();
    } else {
      const params = {
        xd_check: dotclear.nonce,
        f: 'setPostMeta',
        postId: this.post_id,
        metaType: this.meta_type,
        meta: str
      };

      const This = this;
      $.post(this.service_uri, params, function(data) {
        if ($(data).find('rsp').attr('status') == 'ok') {
          This.meta_dialog.val('');
          This.displayMetaList();
        } else {
          window.alert($(data).find('message').text());
        }
      });
    }
  },

  removeMeta: function(meta_id) {
    if (this.post_id == false) {
      let meta = this.splitMetaValues(this.meta_field.val());
      for (let i = 0; i < meta.length; i++) {
        if (meta[i] == meta_id) {
          meta.splice(i, 1);
          break;
        }
      }
      this.meta_field.val(meta.join(','));
      this.displayMetaList();
    } else {
      const text_confirm_msg = this.text_confirm_remove.replace(/%s/, this.meta_type);

      if (window.confirm(text_confirm_msg)) {
        const This = this;
        const params = {
          xd_check: dotclear.nonce,
          f: 'delMeta',
          postId: this.post_id,
          metaId: meta_id,
          metaType: this.meta_type
        };

        $.post(this.service_uri, params, function(data) {
          if ($(data).find('rsp').attr('status') == 'ok') {
            This.displayMetaList();
          } else {
            window.alert($(data).find('message').text());
          }
        });
      }
    }
  },

  splitMetaValues: function(str) {
    function inArray(needle, stack) {
      for (var i = 0; i < stack.length; i++) {
        if (stack[i] == needle) {
          return true;
        }
      }
      return false;
    }

    let res = [];
    let v = str.split(',');
    v.sort();
    for (let i = 0; i < v.length; i++) {
      v[i] = v[i].replace(/^\s*/, '').replace(/\s*$/, '');
      if (v[i] != '' && !inArray(v[i], res)) {
        res.push(v[i]);
      }
    }
    res.sort();
    return res;
  }
};
