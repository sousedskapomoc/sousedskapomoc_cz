SousedskaPomoc.cz
===================

Webova stranka slouzici k organizaci dobrovolniku pro dorucovani zbozi lidem kteri si na nakup nemohou nebo nesmeji dojit.

Jakakoliv pomoc je vitana - cela aplikace bude uvolnena pod licenci GNU/GPL.

Veskere cinnosti spojene s provozovanim tohoto webu (at uz kuryrni prace, prace operatora, vyvojarske prace apd.) jsou provadeny BEZ NAROKU na odmenu.

## Instalace

Nově máme i soubory `Dockerfile` a `docker-compose.yml`, sloužící pro automatizované nasazení celé aplikace (bez DB) na jakýkoliv stroj, kde běží docker.

Jak nasadit:
1. Zkontrolujte, že máte nainstalovaný docker a zkuste spustit `docker --version`
2. Naklonujte si repozitář pomocí git clone
3. V naklonovaném repozitáři spusťte příkaz `docker-compose up`
    * Pokud byste chtěli spustit znovu a od začátku, použijte `docker-compose up --build`, případně nejprve smažte všechny docker images
    * Pokud byste chtěli spustit celý web bez výpisu na příkazovou řádku, spusťte `docker-compose up -d`
4. Pokud si otevřete ve webovém prohlížeči localhost/sousedskapomoc.cz měli byste vidět běžící aplikaci