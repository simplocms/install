@if (isset($AB_Testing))
    <script>
        var emitEvent = function () {
            var uid = '{{ $AB_Testing['uid'] }}';
            var name = '{{ $AB_Testing['name'] }}';
            var version = '{{ $AB_Testing['version'] }}';
            if (typeof ga === "function") {
                ga('send', 'event', 'simploCMS A/B', uid, name, {
                    nonInteraction: true
                });
            }

            if (typeof gtag === "function") {
                gtag('event', uid, {
                    'event_label': name,
                    'event_category': 'simploCMS A/B',
                    'non_interaction': true
                });
            }

            if (typeof _trackEvent === "function") {
                _trackEvent('simploCMS A/B', uid, name, null, true)
            }

            if (typeof dataLayer === "object" && typeof dataLayer.push === "function") {
                dataLayer.push({
                    'event': uid,
                    'event_label': name,
                    'event_category': 'simploCMS A/B',
                    'non_interaction': true
                });
            }

            if (typeof hj === "function") {
                hj('trigger', 'split_test_' + version);
            }
        };

        if (document && typeof document.addEventListener === 'function') {
            document.addEventListener("DOMContentLoaded", function () {
                emitEvent();
            });
        } else if (window && window.onload) {
            window.onload = function () {
                emitEvent();
            };
        }
    </script>
@endif
