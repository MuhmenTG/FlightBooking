import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment.development';
import { AccessTokenResponse } from '../_models/AccessTokenResponse';
import { FlightService } from './flight.service';
import { HotelService } from './hotel.service';

@Injectable({
  providedIn: 'root'
})
export class EnvironmentService {
  private amadeusUrl = environment.amadeusApiUrl;

  private accessTokenParameters = new HttpParams()
    .set('grant_type', 'client_credentials')
    .set('client_id', '73Vz7GstLSf9xaHWx0fPMH6PRg6wYqjT')
    .set('client_secret', 'lSJEDh6AwHwH5omy')

  private httpOptions = {};

  constructor(private http: HttpClient, private _flightService: FlightService, private _hotelService: HotelService) { }

  setHttpOptions(access_token: string): void {
    this.httpOptions = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
        'Authorization': "Bearer " + access_token
      })
    }

    this._flightService.httpOptions = this.httpOptions;
    this._hotelService.httpOptions = this.httpOptions;
  }

  getAccessToken(): Observable<AccessTokenResponse> {
    return this.http.post<AccessTokenResponse>(this.amadeusUrl + "security/oauth2/token", this.accessTokenParameters);
  }
}
