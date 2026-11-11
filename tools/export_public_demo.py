"""Export the seeded, read-only local demo as public HTML without a database dependency."""
import http.cookiejar
import json
import re
import shutil
from collections import deque
from pathlib import Path
from urllib.parse import parse_qs, urlencode, urljoin, urlparse
from urllib.request import HTTPCookieProcessor, build_opener

from bs4 import BeautifulSoup

ROOT = Path(__file__).resolve().parents[1]
DESTINATION = ROOT / "public/demo-publica"
BASE_URL = "http://127.0.0.1:8766"
cookies = http.cookiejar.CookieJar(
    policy=http.cookiejar.DefaultCookiePolicy(secure_protocols=("https", "http"))
)
opener = build_opener(HTTPCookieProcessor(cookies))

login = BeautifulSoup(opener.open(BASE_URL + "/login").read(), "html.parser")
form = login.find("form", action=BASE_URL + "/demo")
if not form or "Explorar demo" not in form.get_text():
    raise RuntimeError("Start the isolated local demo on port 8766 before exporting.")
token = form.find("input", attrs={"name": "_token"})["value"]
opener.open(BASE_URL + "/demo", urlencode({"_token": token}).encode()).read()


def filename(path):
    parsed = urlparse(path)
    page = parse_qs(parsed.query).get("page", ["1"])[0]
    if parsed.path == "/clientes":
        return "index.html" if page == "1" else f"clientes-{int(page)}.html"
    if parsed.path == "/mis-clientes":
        return "mis-clientes.html"
    if parsed.path == "/reporte":
        return "reportes.html"
    if re.fullmatch(r"/clientes/\d+", parsed.path):
        return "cliente-" + parsed.path.rsplit("/", 1)[1] + ".html"
    if re.fullmatch(r"/mis-clientes/\d+", parsed.path):
        return "mi-cliente-" + parsed.path.rsplit("/", 1)[1] + ".html"
    raise ValueError(path)


queue = deque(["/clientes", "/mis-clientes", "/reporte"])
seen = set()
while queue:
    path = queue.popleft()
    if path in seen:
        continue
    seen.add(path)
    document = BeautifulSoup(opener.open(BASE_URL + path).read(), "html.parser")
    if not document.select_one(".cm-demo-notice"):
        raise RuntimeError("Only pages from the read-only demo can be exported.")
    document.html["lang"] = "es"
    document.html["data-clientmanager-demo-version"] = "2"
    document.title.string = "ClientManager · Demo"
    for email in re.findall(r"[\w.+-]+@[\w.-]+\.[A-Za-z]+", document.get_text()):
        if not email.endswith("@example.test") and not email.endswith("@clientmanager.example.test"):
            raise RuntimeError("An unexpected email address was found in the demo.")

    for node in document.select('meta[name="csrf-token"], input[name="_token"], link[rel="modulepreload"], link[rel="preload"]'):
        node.decompose()
    for node in document.find_all("link", rel="stylesheet"):
        if "/build/assets/" in node.get("href", ""):
            node["href"] = "/demo-publica/assets/app.css"
    for node in document.find_all("script"):
        src = node.get("src", "")
        if src:
            if "jquery-3.7.0" in src:
                node["src"] = "/demo-publica/assets/vendor/jquery.min.js"
            elif "chart.js@3.9.1" in src:
                node["src"] = "/demo-publica/assets/vendor/chart.min.js"
            elif "jquery.dataTables" in src:
                node["src"] = "/demo-publica/assets/vendor/datatables.min.js"
            elif "dataTables.bootstrap5" in src:
                node["src"] = "/demo-publica/assets/vendor/datatables-bootstrap.min.js"
            else:
                node.decompose()
        elif "new Chart(" not in node.get_text():
            node.decompose()

    for node in document.select('[data-cm-confirmation], #toast-container'):
        node.decompose()
    for node in document.find_all("img"):
        if "ui-avatars.com" in node.get("src", ""):
            node.decompose()
    for node in document.find_all(True):
        for attr in list(node.attrs):
            if attr.startswith("on") or attr.startswith("x-"):
                del node[attr]

    for link in document.find_all("a", href=True):
        target = urlparse(urljoin(BASE_URL + path, link["href"]))
        if link["href"].startswith("#"):
            continue
        if target.netloc != urlparse(BASE_URL).netloc:
            raise RuntimeError("Unexpected external link in demo.")
        target_path = target.path + ("?" + target.query if target.query else "")
        try:
            name = filename(target_path)
        except ValueError:
            link["href"] = "/login"
            continue
        link["href"] = "/demo-publica/" + name
        queue.append(target_path)

    for form in document.find_all("form"):
        if form.get("method", "get").lower() != "get":
            link = document.new_tag("a", href="/login", attrs={"class": "cm-signout"})
            link.string = "Volver al acceso"
            form.replace_with(link)
        else:
            form["action"] = "/demo-publica/reportes.html"

    refresh = document.select_one(".btn-refresh")
    if refresh:
        refresh["data-demo-refresh"] = ""
    stamp = document.select_one("#ultimo-refresh")
    if stamp:
        stamp.string = "Datos de ejemplo"
    banner = document.select_one(".cm-demo-notice")
    banner.clear()
    banner.string = "Demo · Solo lectura con datos ficticios"
    document.body.append(document.new_tag("script", src="/demo-publica/demo.js", defer=""))
    html = "\n".join(line.rstrip() for line in str(document).splitlines()) + "\n"
    (DESTINATION / filename(path)).write_text(html, encoding="utf-8")

manifest = json.loads((ROOT / "public/build/manifest.json").read_text())
shutil.copyfile(ROOT / "public/build" / manifest["resources/css/app.css"]["file"], DESTINATION / "assets/app.css")
print(f"Exported {len(seen)} synthetic demo pages.")
