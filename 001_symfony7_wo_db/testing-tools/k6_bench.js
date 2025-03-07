import { check } from 'k6';
import http from 'k6/http';
export default function() {
  const res = http.get('http://symfony7site/?firstName=Randomlfirstname&lastName=Randomlastname');
  check(res, {
    'is status 200': (r) => r.status === 200,
    'verify first name on page': (r) =>
        r.body.includes('Randomlfirstname'),
  });
}
