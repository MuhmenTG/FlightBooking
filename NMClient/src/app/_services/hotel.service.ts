import { Injectable } from '@angular/core';
import { environment } from 'src/environments/environment.development';
import { SearchHotelsResponses } from '../_models/Hotels/SearchHotelsResponses';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { SearchHotelsRequest } from '../_models/Hotels/SearchHotelsRequest';
import { HotelInfoResponse } from '../_models/Hotels/HotelInfoResponse';
import { HotelOffer } from '../_models/Hotels/HotelOffer';

@Injectable({
  providedIn: 'root'
})
export class HotelService {
  private apiUrl = environment.apiUrl + "/hotel";

  private httpOptions = {};

  constructor(private http: HttpClient) { }

  setHttpOptions(): void {
    this.httpOptions = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
        'Authorization': "Bearer " + environment.access_token
      })
    }
  }

  getHotels(body: SearchHotelsRequest): Observable<SearchHotelsResponses> {
    return this.http.post<SearchHotelsResponses>(this.apiUrl + "/searchSelectHotel", body, this.httpOptions);
  }

  getHotelInfo(hotelOffer: string): Observable<HotelInfoResponse> {
    return this.http.get(this.apiUrl + "/reviewSelectedHotelOfferInfo/" + hotelOffer);
  }

  getHotelConfirmation(bookingInfo: HotelOffer): Observable<{}> {
    return this.http.post<HotelOffer>(this.apiUrl + "/confirmFlight", JSON.stringify(bookingInfo), this.httpOptions);
  }
}