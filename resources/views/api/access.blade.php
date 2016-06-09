<div class="row">
    <div class="col-xs-12">
        <h3>Parameter</h3>
        <p>
            Zur Einschränkung der ausgegebenen Daten bietet die OParl API die
            folgende Parametersyntax an. Der <code>format</code>-Parameter ist dabei eine Besonderheit dieser Implementierung.
        </p>

        <pre><code class="language-bash">{{ url('/api/v1/') }}/&lt;entity&gt;?[format=&lt;format&gt;][&amp;created_since=&lt;datetime&gt;][&amp;created_until=&lt;datetime&gt;][&amp;modified_since=&lt;datetime&gt;][&amp;modified_until=&lt;datetime&gt;]</code></pre>

        <dl class="dl-horizontal">
            <dt>format</dt>
            <dd>
                <div><strong>Datentyp:&nbsp;</strong><samp>string</samp></div>

                <p>
                    Wählt das gewünschte Format aus. Es ist sowohl der Zugriff auf die <a href="{{ $url }}?format=html">html-Variante dieser Seite</a>
                    als auch auf die <a href="{{ $url }}?format=json" target="_blank">json-Ausgabe</a> möglich.
                </p>
                <p>
                    Wird `format` nicht angegeben, dann wird gemäß der OParl-Spezifikation json ausgegeben.
                </p>
            </dd>

            <dt>created_since</dt>
            <dd>
                <div><strong>Datentyp:&nbsp;</strong><samp>datetime</samp></div>

                <p>
                    Schränkt die Liste auf alle Suchergebnisse ein, die seit einschließlich dem
                    angegebenen Zeitpunkt erstellt wurden.
                </p>
            </dd>

            <dt>created_until</dt>
            <dd>
                <div><strong>Datentyp:&nbsp;</strong><samp>datetime</samp></div>

                <p>
                    Schränkt die Liste auf alle Suchergebnisse ein, die bis einschließlich dem
                    angegebenen Zeitpunkt erstellt wurden.
                </p>
            </dd>

            <dt>modifed_since</dt>
            <dd>
                <div><strong>Datentyp:&nbsp;</strong><samp>datetime</samp></div>

                <p>
                    Schränkt die Liste auf alle Suchergebnisse ein, die seit einschließlich dem
                    angegebenen Zeitpunkt verändert wurden.
                </p>
            </dd>

            <dt>modifed_until</dt>
            <dd>
                <div><strong>Datentyp:&nbsp;</strong><samp>datetime</samp></div>

                <p>
                    Schränkt die Liste auf alle Suchergebnisse ein, die bis einschließlich dem
                    angegebenen Zeitpunkt verändert wurden.
                </p>
            </dd>
        </dl>
    </div>
</div>
