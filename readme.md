SousedskaPomoc.cz
===================

Webova stranka slouzici k organizaci dobrovolniku pro dorucovani zbozi lidem kteri si na nakup nemohou nebo nesmeji dojit.

Jakakoliv pomoc je vitana - cela aplikace bude uvolnena pod licenci GNU/GPL.

Veskere cinnosti spojene s provozovanim tohoto webu (at uz kuryrni prace, prace operatora, vyvojarske prace apd.) jsou provadeny BEZ NAROKU na odmenu.

## Instalace

Nově máme i soubory `Dockerfile` a `docker-compose.yml`, sloužící pro automatizované nasazení celé aplikace (bez DB) na jakýkoliv stroj, kde běží docker.

Jak nasadit:
1. Zkontrolujte, že máte nainstalovaný docker-compose a zkuste spustit `docker-compose --version` ([see more here](https://docs.docker.com/compose/install/))
2. Naklonujte si tento repozitář pomocí `git clone`
3. Pokud ji nemáte, vytvořte si proměnnou prostředí PORT pomocí export PORT=80 (nebo třeba 8080)
4. V naklonovaném repozitáři spusťte ze složky docker příkaz `docker-compose up`
    * Pokud byste chtěli spustit znovu a od začátku, použijte `docker-compose up --build`, případně nejprve smažte všechny docker images
    * Pokud byste chtěli spustit celý web bez výpisu na příkazovou řádku, spusťte `docker-compose up -d`
5. Aplikace by nyní měla běžet na vašem zařízení (nějakou dobu trvá, než composer nainstaluje všechny dependencies)


## Troubleshooting

### Version in docker-compose.yml is unsupported
```
ERROR: Version in "./docker-compose.yml" is unsupported. You might be seeing this error because you're using the wrong Compose file version. Either specify a supported version (e.g "2.2" or "3.3") and place your service definitions under the `services` key, or omit the `version` key and place your service definitions at the root of the file to use version 1.
```
Tato chyba nejspíše signalizuje starou verzi docker-compose, která nepodporuje aktuální verzi docker-compose souboru.
Stačí nainstalovat novou verzi docker-compose z <https://docs.docker.com/compose/install/> (předtím možná bude nutno odinstalovat starou verzi docker-compose)


### ERROR: Couldn't connect to Docker daemon
```
ERROR: Couldn't connect to Docker daemon at http+docker://localhost - is it running?
```
Docker neběží. Může být způsobeno tím, že docker běží pod rootem a docker-compose pouštíme pod svým uživatelem.
Řešením je pustit docker pod lokálním uživatelem (případně pustit docker-compose pod rootem, nedoporučeno)
