#!/usr/bin/env python3
from http.server import SimpleHTTPRequestHandler, HTTPServer
import os
import sys

PORT = int(os.environ.get('PORT', '8000'))


class UTF8Handler(SimpleHTTPRequestHandler):
    def send_head(self):
        path = self.translate_path(self.path)
        if os.path.isdir(path):
            for index in ("index.html", "index.htm"):
                index_path = os.path.join(path, index)
                if os.path.exists(index_path):
                    path = index_path
                    break
        ctype = self.guess_type(path)
        try:
            f = open(path, 'rb')
        except OSError:
            self.send_error(404, "File not found")
            return None
        self.send_response(200)
        if ctype.startswith('text/html'):
            self.send_header('Content-Type', 'text/html; charset=utf-8')
        elif ctype.startswith('text/'):
            self.send_header('Content-Type', f'{ctype}; charset=utf-8')
        else:
            self.send_header('Content-Type', ctype)
        fs = os.fstat(f.fileno())
        self.send_header("Content-Length", str(fs.st_size))
        self.send_header("Last-Modified", self.date_time_string(fs.st_mtime))
        self.end_headers()
        return f


def main():
    httpd = HTTPServer(("127.0.0.1", PORT), UTF8Handler)
    print(f"Serving on http://127.0.0.1:{PORT}")
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        pass
    finally:
        httpd.server_close()


if __name__ == '__main__':
    sys.exit(main())

