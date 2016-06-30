function mysql_dt_to_js_date(e){var t=e.split(/[- :]/);return new Date(t[0],t[1]-1,t[2],t[3],t[4],t[5])}function get_screen_size(){return $("#screen-size div:visible:first").attr("data-size")}function on_window_resize(){window_w=$("body").width(),window_h=$("body").height(),screen_size=get_screen_size(),all_match_col_h()}function cookie_views(){var e=$("body").attr("class"),t=e.split(" "),r=[];for(var n in t)t[n].startsWith("board-view-")&&r.push(t[n]);Cookies.set("view",r.join(" "))}function all_match_col_h(){for(var e in boards){var t=boards[e];t.match_col_h()}}function build_url(){var e=window.location.href,t=e.split("?"),r=t[0]+"?"+decodeURIComponent($.param(url_params));return r}function update_url(){var e=build_url();window.history.replaceState("","",e),update_page_title()}function update_page_title(){if("undefined"!=typeof url_params.board_id){var e=boards[url_params.board_id];if("undefined"==typeof e)return!1;document.title="{0} | {1}".sprintf(e.record.title(),text.kanban)}}function usurp(e){for(var t=e,r=e.childNodes.length-1;r>=0;r--){var n=e.removeChild(e.childNodes[r]);e.parentNode.insertBefore(n,t),t=n}e.parentNode.removeChild(e)}function strip_tags(e,t){"undefined"==typeof t&&(allowed_tag=["B","I","STRONG","EM","BR"]),$("*",e).each(function(){-1==allowed_tag.indexOf(this.nodeName)&&usurp(this)})}function remove_attributes_from_tags(e){$("*",e).each(function(){for(var e=this.attributes,t=e.length;t--;)this.removeAttributeNode(e[t])})}function sanitize(e){strip_tags(e),remove_attributes_from_tags(e),e.html($.trim(e.html().replace(/&nbsp;/gi," ")))}function encode_emails(e){if("undefined"==typeof e)return e;if(""==e)return e;var t=/(<a href(?:(?!<\/a\s*>).)*)?([\w.-]+@[\w.-]+\.[\w.-]+)/gi;return e.replace(t,function(e,t){return t?e:'<a href="mailto:'+e+'"  contenteditable="false">'+e+"</a>"})}function encode_urls(e){if("undefined"==typeof e)return e;if(""==e)return e;var t=/(<a href(?:(?!<\/a\s*>).)*)?(http[^\s\<]+)/gi;return e.replace(t,function(e,t){return e=e.replace("&nbsp;",""),t?e:'<a href="'+$.trim(e)+'"  contenteditable="false" target="_blank">'+$.trim(e)+"</a>"})}function encode_urls_emails(e){e.html(encode_emails(e.html())),e.html(encode_urls(e.html()))}function placeCaretAtEnd(e){if(e.focus(),"undefined"!=typeof window.getSelection&&"undefined"!=typeof document.createRange){var t=document.createRange();t.selectNodeContents(e),t.collapse(!1);var r=window.getSelection();r.removeAllRanges(),r.addRange(t)}else if("undefined"!=typeof document.body.createTextRange){var n=document.body.createTextRange();n.moveToElementText(e),n.collapse(!1),n.select()}}function growl_response_message(e){try{growl(e.data.message)}catch(t){}}function growl(e){"undefined"!=typeof e&&""!=e&&$.bootstrapGrowl(e,{type:"info",allow_dismiss:!0})}function format_hours(e){if(0>=e)return"0 <sub>h</sub> ";var t=Math.round(60*e),r=Math.floor(t/480),n=t%480,o=Math.floor(n/60),i=n%60,a=Math.floor(i),s="";return r>0&&(s+="{0} <sub>d</sub> ".sprintf(r)),o>0&&(s+="{0} <sub>h</sub> ".sprintf(o)),a>0&&(s+="{0} <sub>m</sub> ".sprintf(a)),""===s&&(s=format_hours(0)),s}function obj_order_by_key(e,t){"undefined"==typeof t&&(t=!1);var r=Object.keys(e);r.sort(function(e,t){return e=parseInt(e),t=parseInt(t),t>e?-1:e>t?1:0});var n=[];for(var o in r)n[r[o]]=e[r[o]];return t&&n.reverse(),n}function obj_order_by_prop(e,t,r){"undefined"==typeof r&&(r=!1);var n=$.map(e,function(e,t){return[e]});return n.sort(function(e,r){return e[t]-r[t]}),r&&n.reverse(),n}String.prototype.sprintf=function(){var e=this;for(var t in arguments)e=e.replace("{"+t+"}",arguments[t]);return e},String.prototype.stripslashes=function(){return(this+"").replace(/\\(.?)/g,function(e,t){switch(t){case"\\":return"\\";case"0":return"\x00";case"":return"";default:return t}})},Number.prototype.padZero=function(e){var t=String(this),r="0";for(e=e||2;t.length<e;)t=r+t;return t};