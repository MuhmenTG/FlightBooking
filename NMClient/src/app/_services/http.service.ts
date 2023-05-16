import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { AccessTokenResponse } from '../_models/AccessTokenResponse';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment.development';

@Injectable({
  providedIn: 'root'
})
export class HttpService {
  private amadeusUrl = environment.amadeusApiUrl;
  static instance: HttpService;

  private accessTokenParameters = new HttpParams()
    .set('grant_type', 'client_credentials')
    .set('client_id', '73Vz7GstLSf9xaHWx0fPMH6PRg6wYqjT')
    .set('client_secret', 'lSJEDh6AwHwH5omy')

  protected httpOptions = {
    headers: new HttpHeaders({
      'Content-Type': 'application/json'
    })
  }

  protected static httpOptionsBearer = {}

  protected http: HttpClient;

  constructor(http: HttpClient) {
    this.http = http;
  }

  setHttpOptions(access_token: string): void {
    HttpService.httpOptionsBearer = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
        'Authorization': "Bearer " + access_token
      })
    }
  }

  getAccessToken(): Observable<AccessTokenResponse> {
    return this.http.post<AccessTokenResponse>(this.amadeusUrl + "security/oauth2/token", this.accessTokenParameters);
  }
}
