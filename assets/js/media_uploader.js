/**
 * Open the wp.media dialog and handles callbacks
 */
function wp_media_dialog() {

    // Extend the wp.media object
    mediaUploader = wp.media.frames.file_frame = wp.media({
        title: 'Choose Image',
        button: {
            text: 'Choose Image'
        },
        multiple: false
    });

    // When a picture is selected, set the picture as main
    mediaUploader.on('select', function () {
        attachment = mediaUploader.state().get('selection').first().toJSON();
        jQuery('#main-picture').attr('src', attachment.url);
        jQuery('#main-picture').attr('data-id', attachment.id);

        jQuery.ajax({
            type: 'POST',
            data: {
                action: 'set_main_picture',
                id: attachment.id
            },
            url: '/wp-admin/admin-ajax.php'
        });
    });

    // Extending to delete main picture after the user deletes an attachment
    jQuery.extend(wp.media.model.Attachment.prototype, {
        sync: function (method, model, options) {
            // If the attachment does not yet have an `id`, return an instantly
            // rejected promise. Otherwise, all of our requests will fail.
            if (_.isUndefined(this.id)) {
                return jQuery.Deferred().rejectWith(this).promise();
            }

            // Overload the `read` request so Attachment.fetch() functions correctly.
            if ('read' === method) {
                options = options || {};
                options.context = this;
                options.data = _.extend(options.data || {}, {
                    action: 'get-attachment',
                    id: this.id
                });
                return wp.media.ajax(options);

                // Overload the `update` request so properties can be saved.
            } else if ('update' === method) {
                console.log("subir");
                // If we do not have the necessary nonce, fail immeditately.
                if (!this.get('nonces') || !this.get('nonces').update) {
                    return $.Deferred().rejectWith(this).promise();
                }

                options = options || {};
                options.context = this;

                // Set the action and ID.
                options.data = _.extend(options.data || {}, {
                    action: 'save-attachment',
                    id: this.id,
                    nonce: this.get('nonces').update,
                    post_id: wp.media.model.settings.post.id
                });

                // Record the values of the changed attributes.
                if (model.hasChanged()) {
                    options.data.changes = {};

                    _.each(model.changed, function (value, key) {
                        options.data.changes[key] = this.get(key);
                    }, this);
                }

                return wp.media.ajax(options);

                // Overload the `delete` request so attachments can be removed.
                // This will permanently delete an attachment.
            } else if ('delete' === method) {
                options = options || {};

                if (!options.wait) {
                    this.destroyed = true;
                }

                options.context = this;
                options.data = _.extend(options.data || {}, {
                    action: 'delete-post',
                    id: this.id,
                    _wpnonce: this.get('nonces')['delete']
                });

                return wp.media.ajax(options).done(function () {
                    this.destroyed = true;

                    if (this.id == jQuery('#main-picture').attr('data-id')) {
                        jQuery('#main-picture').attr('src', 'http://0.gravatar.com/avatar/?s=150&d=mm');
                        jQuery('#main-picture').attr('data-id', '');

                        jQuery.ajax({
                            type: 'POST',
                            data: {
                                action: 'delete_main_picture',
                                id: this.id
                            },
                            url: '/wp-admin/admin-ajax.php'
                        });
                    }

                }).fail(function () {
                    this.destroyed = false;
                });

                // Otherwise, fall back to `Backbone.sync()`.
            } else {
                /**
                 * Call `sync` directly on Backbone.Model
                 */
                return Backbone.Model.prototype.sync.apply(this, arguments);
            }
        }
    });

    mediaUploader.open();
}